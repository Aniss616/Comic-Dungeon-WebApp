import * as THREE from 'three';

export function initRain() {
    const canvas = document.getElementById('rain-canvas');
    if (!canvas) return;

    const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: false });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setSize(window.innerWidth, window.innerHeight);

    let W = window.innerWidth;
    let H = window.innerHeight;

    const scene  = new THREE.Scene();
    const camera = new THREE.OrthographicCamera(W/-2, W/2, H/2, H/-2, 0.1, 10);
    camera.position.z = 1;

    const DROP_COUNT = 300;
    const drops = Array.from({ length: DROP_COUNT }, () => {
        const length = 14 + Math.random() * 30;
        return {
            x:      (Math.random() - 0.5) * W,
            y:      (Math.random() - 0.5) * H,
            speed:  200 + Math.random() * 350,
            length,
        };
    });

    const positions = new Float32Array(DROP_COUNT * 6);
    const geometry  = new THREE.BufferGeometry();
    geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));

    const material = new THREE.LineBasicMaterial({ color: 0x7A99BB, transparent: true, opacity: 0.6 });
    scene.add(new THREE.LineSegments(geometry, material));

    let lastTime = performance.now();
    let animId;

    function animate() {
        animId = requestAnimationFrame(animate);
        const now   = performance.now();
        const delta = Math.min((now - lastTime) / 1000, 0.05);
        lastTime    = now;

        drops.forEach((d, i) => {
            d.y -= d.speed * delta;
            if (d.y - d.length < H / -2) {
                d.y = H / 2 + d.length;
                d.x = (Math.random() - 0.5) * W;
            }
            const idx = i * 6;
            positions[idx]   = d.x;  positions[idx+1] = d.y;             positions[idx+2] = 0;
            positions[idx+3] = d.x;  positions[idx+4] = d.y - d.length;  positions[idx+5] = 0;
        });

        geometry.attributes.position.needsUpdate = true;
        renderer.render(scene, camera);
    }

    animate();

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) { cancelAnimationFrame(animId); }
        else { lastTime = performance.now(); animate(); }
    });

    window.addEventListener('resize', () => {
        W = window.innerWidth; H = window.innerHeight;
        renderer.setSize(W, H);
        camera.left = W/-2; camera.right = W/2;
        camera.top  = H/2;  camera.bottom = H/-2;
        camera.updateProjectionMatrix();
    });
}