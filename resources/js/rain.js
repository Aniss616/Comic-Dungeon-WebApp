import * as THREE from 'three';

let renderer = null;
let animId   = null;
let lastFrame = 0;
const TARGET_FPS = 30;
const FRAME_MS   = 1000 / TARGET_FPS;

export function initRain(canvas) {
  const W = canvas.offsetWidth  || window.innerWidth;
  const H = canvas.offsetHeight || window.innerHeight;

  renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: false });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 1.5)); // cap at 1.5x
  renderer.setSize(W, H);

  const scene  = new THREE.Scene();
  const camera = new THREE.OrthographicCamera(-W / 2, W / 2, H / 2, -H / 2, 0.1, 100);
  camera.position.z = 10;

  // Build drops
  const DROP_COUNT = 150;
  const positions  = new Float32Array(DROP_COUNT * 6); // 2 points × 3 floats each

  function randomDrop(i) {
    const x   = (Math.random() - 0.5) * W * 1.1;
    const y   = (Math.random() - 0.5) * H * 1.1;
    const len = 8 + Math.random() * 14;
    // p1
    positions[i * 6 + 0] = x;
    positions[i * 6 + 1] = y;
    positions[i * 6 + 2] = 0;
    // p2
    positions[i * 6 + 3] = x - len * 0.22;
    positions[i * 6 + 4] = y - len;
    positions[i * 6 + 5] = 0;
  }

  for (let i = 0; i < DROP_COUNT; i++) randomDrop(i);

  const geo = new THREE.BufferGeometry();
  geo.setAttribute('position', new THREE.BufferAttribute(positions, 3));

  const mat = new THREE.LineBasicMaterial({
    color: 0x8899aa,
    transparent: true,
    opacity: 0.22,
  });

  const rain = new THREE.LineSegments(geo, mat);
  scene.add(rain);

  // Per-drop speed
  const speeds = Array.from({ length: DROP_COUNT }, () => 3.5 + Math.random() * 5);

  function animate(ts) {
    animId = requestAnimationFrame(animate);
    if (ts - lastFrame < FRAME_MS) return;
    lastFrame = ts;

    const pos = geo.attributes.position.array;
    const halfH = H / 2;
    const halfW = W / 2;

    for (let i = 0; i < DROP_COUNT; i++) {
      const sp = speeds[i];
      pos[i * 6 + 1] -= sp;
      pos[i * 6 + 4] -= sp;

      if (pos[i * 6 + 1] < -halfH) {
        const nx = (Math.random() - 0.5) * W * 1.1;
        const ny = halfH + Math.random() * halfH * 0.5;
        const len = 8 + Math.random() * 14;
        pos[i * 6 + 0] = nx;
        pos[i * 6 + 1] = ny;
        pos[i * 6 + 3] = nx - len * 0.22;
        pos[i * 6 + 4] = ny - len;
      }
    }

    geo.attributes.position.needsUpdate = true;
    renderer.render(scene, camera);
  }

  function handleResize() {
    const nW = canvas.offsetWidth;
    const nH = canvas.offsetHeight;
    if (!nW || !nH) return;
    renderer.setSize(nW, nH);
    camera.left   = -nW / 2;
    camera.right  =  nW / 2;
    camera.top    =  nH / 2;
    camera.bottom = -nH / 2;
    camera.updateProjectionMatrix();
  }

  // Pause on hidden tab
  document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
      if (animId) { cancelAnimationFrame(animId); animId = null; }
    } else {
      if (!animId) animId = requestAnimationFrame(animate);
    }
  });

  window.addEventListener('resize', handleResize, { passive: true });

  animId = requestAnimationFrame(animate);

  return {
    destroy() {
      if (animId) cancelAnimationFrame(animId);
      geo.dispose();
      mat.dispose();
      renderer.dispose();
      animId = null;
    }
  };
}