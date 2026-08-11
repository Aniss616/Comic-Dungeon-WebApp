let animId = null;
let offscreen = null;
let W = 0, H = 0;
let lastFrame = 0;
const TARGET_FPS = 30;
const FRAME_MS = 1000 / TARGET_FPS;

const STAR_COUNT = 180;
const stars = [];

const NEBULA_BLOBS = [
  { x: 0.15, y: 0.25, r: 0.28, color: 'rgba(26,138,60,0.045)' },
  { x: 0.75, y: 0.15, r: 0.22, color: 'rgba(46,127,212,0.04)' },
  { x: 0.55, y: 0.65, r: 0.32, color: 'rgba(26,138,60,0.03)' },
  { x: 0.88, y: 0.7, r: 0.18, color: 'rgba(100,220,140,0.025)' },
];

const PLANET = {
  xFrac: 0.78,
  yFrac: 0.38,
  rFrac: 0.13,
  ringTilt: 0.28,
};

// Shooting star pool
const shootingStars = [];

let tick = 0;

export function initCosmic(canvas) {
  const ctx = canvas.getContext('2d', { alpha: true });

  function resize() {
    W = canvas.offsetWidth;
    H = canvas.offsetHeight;
    canvas.width = W;
    canvas.height = H;
    buildStars();
    buildOffscreen();
  }

  function buildStars() {
    stars.length = 0;

    for (let i = 0; i < STAR_COUNT; i++) {
      stars.push({
        x: Math.random() * W,
        y: Math.random() * H,
        r: 0.5 + Math.random() * 1.4,
        phase: Math.random() * Math.PI * 2,

        // Mix of slow pulse + fast shimmer per star
        speed: 0.8 + Math.random() * 3.5,
        bright: 0.5 + Math.random() * 0.5,

        // Pulse scale: some stars physically grow/shrink
        scaleAmp: Math.random() < 0.3
          ? 0.5 + Math.random() * 0.8
          : 0,

        scaleSpeed: 0.6 + Math.random() * 1.2,
      });
    }
  }

  function buildOffscreen() {
    offscreen = document.createElement('canvas');
    offscreen.width = W;
    offscreen.height = H;

    const oc = offscreen.getContext('2d');

    // Deep space gradient background
    const bg = oc.createLinearGradient(0, 0, 0, H);
    bg.addColorStop(0, '#050810');
    bg.addColorStop(0.5, '#080D18');
    bg.addColorStop(1, '#0A1520');

    oc.fillStyle = bg;
    oc.fillRect(0, 0, W, H);

    // Nebula blobs
    for (const b of NEBULA_BLOBS) {
      const gx = b.x * W;
      const gy = b.y * H;
      const gr = b.r * Math.min(W, H);

      const g = oc.createRadialGradient(
        gx, gy, 0,
        gx, gy, gr
      );

      g.addColorStop(0, b.color);
      g.addColorStop(
        0.5,
        b.color.replace(/[\d.]+\)$/, '0.01)')
      );
      g.addColorStop(1, 'rgba(0,0,0,0)');

      oc.fillStyle = g;
      oc.beginPath();
      oc.arc(gx, gy, gr, 0, Math.PI * 2);
      oc.fill();
    }

  }

  function drawRingHalf(ctx2, px, py, pr, front = false) {
    ctx2.save();

    ctx2.translate(px, py);
    ctx2.rotate(PLANET.ringTilt);
    ctx2.scale(1, 0.28);

    const ringOuter = pr * 1.85;
    const ringInner = pr * 1.25;

    const ringGrad = ctx2.createRadialGradient(
      0, 0,
      ringInner,
      0, 0,
      ringOuter
    );

    ringGrad.addColorStop(0, 'rgba(46,127,212,0.0)');
    ringGrad.addColorStop(0.15, 'rgba(46,127,212,0.35)');
    ringGrad.addColorStop(0.55, 'rgba(100,200,120,0.22)');
    ringGrad.addColorStop(1, 'rgba(0,0,0,0)');

    ctx2.beginPath();

    if (!front) {
      ctx2.arc(
        0,
        0,
        ringOuter,
        Math.PI,
        Math.PI * 2
      );

      ctx2.arc(
        0,
        0,
        ringInner,
        Math.PI * 2,
        Math.PI,
        true
      );
    } else {
      ctx2.arc(
        0,
        0,
        ringOuter,
        0,
        Math.PI
      );

      ctx2.arc(
        0,
        0,
        ringInner,
        Math.PI,
        0,
        true
      );
    }

    ctx2.fillStyle = ringGrad;
    ctx2.fill();

    ctx2.restore();
  }

  function drawPlanet(ctx2) {
    const px = PLANET.xFrac * W;
    const py = PLANET.yFrac * H;
    const pr = PLANET.rFrac * Math.min(W, H);

    // Outer glow
    const aura = ctx2.createRadialGradient(
      px,
      py,
      pr * 0.8,
      px,
      py,
      pr * 2.2
    );

    aura.addColorStop(0, 'rgba(26,138,60,0.18)');
    aura.addColorStop(0.4, 'rgba(26,138,60,0.06)');
    aura.addColorStop(1, 'rgba(0,0,0,0)');

    ctx2.fillStyle = aura;
    ctx2.beginPath();
    ctx2.arc(px, py, pr * 2.2, 0, Math.PI * 2);
    ctx2.fill();

    // Back half of ring
    drawRingHalf(ctx2, px, py, pr, false);

    // Planet base
    ctx2.beginPath();
    ctx2.arc(px, py, pr, 0, Math.PI * 2);
    ctx2.fillStyle = '#040E09';
    ctx2.fill();

    // Planet sphere gradient
    const planetGrad = ctx2.createRadialGradient(
      px - pr * 0.3,
      py - pr * 0.25,
      pr * 0.05,
      px,
      py,
      pr
    );

    planetGrad.addColorStop(0, '#1E5C38');
    planetGrad.addColorStop(0.35, '#163D28');
    planetGrad.addColorStop(0.65, '#0E2B1C');
    planetGrad.addColorStop(1, '#071510');

    ctx2.beginPath();
    ctx2.arc(px, py, pr, 0, Math.PI * 2);
    ctx2.fillStyle = planetGrad;
    ctx2.fill();

    // Surface bands
    ctx2.save();

    ctx2.beginPath();
    ctx2.arc(px, py, pr, 0, Math.PI * 2);
    ctx2.clip();

    const bands = [
      {
        y: -0.3,
        h: 0.12,
        color: 'rgba(46,127,212,0.09)'
      },
      {
        y: 0.1,
        h: 0.08,
        color: 'rgba(26,138,60,0.07)'
      },
      {
        y: 0.3,
        h: 0.15,
        color: 'rgba(46,127,212,0.06)'
      },
    ];

    for (const band of bands) {
      const bh = band.h * pr * 2;
      const by = py + band.y * pr - bh / 2;

      const bg2 = ctx2.createLinearGradient(
        px - pr,
        by,
        px + pr,
        by
      );

      bg2.addColorStop(0, 'rgba(0,0,0,0)');
      bg2.addColorStop(0.3, band.color);
      bg2.addColorStop(0.7, band.color);
      bg2.addColorStop(1, 'rgba(0,0,0,0)');

      ctx2.fillStyle = bg2;
      ctx2.fillRect(
        px - pr,
        by,
        pr * 2,
        bh
      );
    }

    ctx2.restore();

    // Shadow
    const shadow = ctx2.createRadialGradient(
      px + pr * 0.5,
      py,
      pr * 0.1,
      px + pr * 0.5,
      py,
      pr * 1.4
    );

    shadow.addColorStop(0, 'rgba(0,0,0,0)');
    shadow.addColorStop(0.5, 'rgba(0,0,0,0.25)');
    shadow.addColorStop(1, 'rgba(0,0,0,0.7)');

    ctx2.save();

    ctx2.beginPath();
    ctx2.arc(px, py, pr, 0, Math.PI * 2);
    ctx2.clip();

    ctx2.fillStyle = shadow;
    ctx2.fillRect(
      px - pr,
      py - pr,
      pr * 2,
      pr * 2
    );

    ctx2.restore();

    // Specular highlight
    ctx2.save();

    ctx2.beginPath();
    ctx2.arc(px, py, pr, 0, Math.PI * 2);
    ctx2.clip();

    const spec = ctx2.createRadialGradient(
      px - pr * 0.32,
      py - pr * 0.3,
      0,
      px - pr * 0.32,
      py - pr * 0.3,
      pr * 0.6
    );

    spec.addColorStop(0, 'rgba(120,255,160,0.18)');
    spec.addColorStop(0.5, 'rgba(120,255,160,0.04)');
    spec.addColorStop(1, 'rgba(0,0,0,0)');

    ctx2.fillStyle = spec;

    ctx2.fillRect(
      px - pr,
      py - pr,
      pr * 2,
      pr * 2
    );

    ctx2.restore();

    // Front half of ring
    drawRingHalf(ctx2, px, py, pr, true);
  }

  // Shooting Stars
  function spawnShootingStar() {
    const angle =
      Math.PI / 5 +
      (Math.random() - 0.5) * 0.4;

    const len =
      120 +
      Math.random() * 160;

    const sx =
      Math.random() * W * 0.7;

    const sy =
      Math.random() * H * 0.5;

    shootingStars.push({
      sx,
      sy,
      ex: sx + Math.cos(angle) * len,
      ey: sy + Math.sin(angle) * len,

      // progress 0→1 over lifetime
      t: 0,

      // fast: crosses full length in ~18–28 frames at 30fps
      speed:
        0.035 +
        Math.random() * 0.03,

      len,
      angle,
    });
  }

  function drawShootingStars(ctx2) {
    for (
      let i = shootingStars.length - 1;
      i >= 0;
      i--
    ) {
      const s = shootingStars[i];

      s.t += s.speed;

      if (s.t >= 1) {
        shootingStars.splice(i, 1);
        continue;
      }

      // Head position along the path
      const hx =
        s.sx +
        Math.cos(s.angle) *
          s.len *
          s.t;

      const hy =
        s.sy +
        Math.sin(s.angle) *
          s.len *
          s.t;

      // Tail: trails 30% of total length behind head
      const tailFrac =
        Math.max(0, s.t - 0.30);

      const tx =
        s.sx +
        Math.cos(s.angle) *
          s.len *
          tailFrac;

      const ty =
        s.sy +
        Math.sin(s.angle) *
          s.len *
          tailFrac;

      // Fade out as it nears the end
      const globalAlpha =
        s.t < 0.75
          ? 1
          : 1 -
            (s.t - 0.75) / 0.25;

      ctx2.save();

      ctx2.globalAlpha = globalAlpha;

      // Outer glow stroke
      const grad =
        ctx2.createLinearGradient(
          tx,
          ty,
          hx,
          hy
        );

      grad.addColorStop(
        0,
        'rgba(180,255,220,0)'
      );

      grad.addColorStop(
        0.6,
        'rgba(180,255,220,0.25)'
      );

      grad.addColorStop(
        1,
        'rgba(255,255,255,0.9)'
      );

      ctx2.beginPath();
      ctx2.moveTo(tx, ty);
      ctx2.lineTo(hx, hy);

      ctx2.strokeStyle = grad;
      ctx2.lineWidth = 4;
      ctx2.shadowBlur = 18;
      ctx2.shadowColor = '#90FFD0';

      ctx2.stroke();

      // Bright core stroke
      const core =
        ctx2.createLinearGradient(
          tx,
          ty,
          hx,
          hy
        );

      core.addColorStop(
        0,
        'rgba(255,255,255,0)'
      );

      core.addColorStop(
        0.5,
        'rgba(255,255,255,0.5)'
      );

      core.addColorStop(
        1,
        'rgba(255,255,255,1)'
      );

      ctx2.beginPath();
      ctx2.moveTo(tx, ty);
      ctx2.lineTo(hx, hy);

      ctx2.strokeStyle = core;
      ctx2.lineWidth = 1.2;
      ctx2.shadowBlur = 8;
      ctx2.shadowColor = '#FFFFFF';

      ctx2.stroke();

      // Bright head dot
      ctx2.beginPath();
      ctx2.arc(
        hx,
        hy,
        2,
        0,
        Math.PI * 2
      );

      ctx2.fillStyle = '#FFFFFF';
      ctx2.shadowBlur = 12;
      ctx2.shadowColor = '#B4FFC8';

      ctx2.fill();

      ctx2.restore();
    }
  }

  // DRAW LOOP
  function draw(ts) {
    if (!W || !H) return;

    if (ts - lastFrame < FRAME_MS) return;

    lastFrame = ts;
    tick += 0.016;

    ctx.clearRect(0, 0, W, H);

    if (offscreen) {
      ctx.drawImage(offscreen, 0, 0);
    }

    // BACKGROUND STARS
    for (const s of stars) {
      const alpha =
        s.bright *
        (
          0.45 +
          0.55 *
            Math.sin(
              tick * s.speed +
              s.phase
            )
        );

      // Pulse scale
      const scale =
        s.scaleAmp > 0
          ? 1 +
            s.scaleAmp *
              Math.abs(
                Math.sin(
                  tick * s.scaleSpeed +
                  s.phase
                )
              )
          : 1;

      const r = s.r * scale;

      // Glow
      const glow =
        ctx.createRadialGradient(
          s.x,
          s.y,
          0,
          s.x,
          s.y,
          r * 3.5
        );

      glow.addColorStop(
        0,
        `rgba(180,220,255,${(
          alpha * 0.35
        ).toFixed(2)})`
      );

      glow.addColorStop(
        1,
        'rgba(180,220,255,0)'
      );

      ctx.beginPath();
      ctx.arc(
        s.x,
        s.y,
        r * 3.5,
        0,
        Math.PI * 2
      );

      ctx.fillStyle = glow;
      ctx.fill();

      // Core
      ctx.beginPath();
      ctx.arc(
        s.x,
        s.y,
        r,
        0,
        Math.PI * 2
      );

      ctx.fillStyle =
        `rgba(255,255,255,${alpha.toFixed(2)})`;

      ctx.fill();
    }

    // SHOOTING STARS
    if (Math.random() < 0.012) {
      spawnShootingStar();
    }

    drawShootingStars(ctx);

    drawPlanet(ctx);
  }

  function loop(ts) {
    draw(ts);
    animId = requestAnimationFrame(loop);
  }

  function destroy() {
    if (animId) {
      cancelAnimationFrame(animId);
    }

    animId = null;
    offscreen = null;
    shootingStars.length = 0;
  }

  document.addEventListener(
    'visibilitychange',
    () => {
      if (document.hidden) {
        if (animId) {
          cancelAnimationFrame(animId);
          animId = null;
        }
      } else {
        if (!animId) {
          animId =
            requestAnimationFrame(loop);
        }
      }
    }
  );

  window.addEventListener(
    'resize',
    resize,
    { passive: true }
  );

  resize();
  animId = requestAnimationFrame(loop);

  return {
    destroy,
    resize
  };
}