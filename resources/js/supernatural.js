let animId = null;
let W = 0, H = 0;
let lastFrame = 0;
const FRAME_MS = 1000 / 30;

let gravestones = [], fogParticles = [], stars = [], candles = [];
let lightning = { active: false, alpha: 0, timer: 0, nextFlash: 200 };
let tick = 0;

// Offscreen cache for the static ground layer
let groundCanvas = null;
let groundCtx = null;

// Cached gradients
let skyGradient = null;
let moonCorona = null;
let moonBody = null;
let moonMist = null;
let moonX = 0, moonY = 0, moonR = 0;

// Tunable counts
const GRAVESTONE_COUNTS = { back: 10, mid: 8, front: 6 };
const FOG_COUNT = 14;
const STAR_COUNT = 40;
const CANDLE_COUNT = 18;

export function initSupernatural(canvas) {
  const ctx = canvas.getContext('2d', { alpha: true });

  function resize() {
    W = canvas.offsetWidth;
    H = canvas.offsetHeight;

    canvas.width = W;
    canvas.height = H;

    if (!groundCanvas) {
      groundCanvas = document.createElement('canvas');
      groundCtx = groundCanvas.getContext('2d', { alpha: true });
    }

    groundCanvas.width = W;
    groundCanvas.height = H;

    build();
  }

  function build() {
    buildStars();
    buildGravestones();
    buildFog();
    buildCandles();
    buildGroundLayer();
    buildSkyAndMoon();
  }

  // STARS
  function buildStars() {
    stars = [];

    for (let i = 0; i < STAR_COUNT; i++) {
      stars.push({
        x: (i * 197.3) % W,
        y: (i * 131.7) % (H * .46),
        r: .5 + (i % 3) * .35,
        phase: Math.random() * Math.PI * 2,
        spd: .02 + Math.random() * .03,
        base: .32 + Math.random() * .3
      });
    }
  }

  // GRAVESTONES
  function buildGravestones() {
    gravestones = [];

    const rows = [
      {
        n: GRAVESTONE_COUNTS.back,
        yFrac: .535,
        xPad: .01,
        xSpread: .98,
        wr: [12, 22],
        hr: [18, 40],
        lean: .20,
        archChance: .5,
        row: 0
      },
      {
        n: GRAVESTONE_COUNTS.mid,
        yFrac: .635,
        xPad: .03,
        xSpread: .94,
        wr: [16, 29],
        hr: [24, 52],
        lean: .18,
        archChance: .55,
        row: 1
      },
      {
        n: GRAVESTONE_COUNTS.front,
        yFrac: .735,
        xPad: .05,
        xSpread: .90,
        wr: [20, 36],
        hr: [30, 64],
        lean: .15,
        archChance: .6,
        row: 2
      }
    ];

    rows.forEach(cfg => {
      for (let i = 0; i < cfg.n; i++) {
        const t = cfg.n > 1 ? i / (cfg.n - 1) : 0;

        gravestones.push({
          x: W * cfg.xPad +
             t * W * cfg.xSpread +
             (Math.random() - .5) * 26,

          y: H * cfg.yFrac +
             (Math.random() - .5) * 10,

          w: cfg.wr[0] +
             Math.random() * (cfg.wr[1] - cfg.wr[0]),

          h: cfg.hr[0] +
             Math.random() * (cfg.hr[1] - cfg.hr[0]),

          lean: (Math.random() - .5) * cfg.lean,
          row: cfg.row,
          type: Math.random() < cfg.archChance ? 'arch' : 'rect',
          detail: Math.floor(Math.random() * 3)
        });
      }
    });

    gravestones.sort((a, b) => a.y - b.y);
  }

  // FOG
  function buildFog() {
    fogParticles = [];

    for (let i = 0; i < FOG_COUNT; i++) {
      fogParticles.push(makeFog(true));
    }
  }

  function makeFog(rand = false) {
    const r = 90 + Math.random() * 170;
    const alpha = .032 + Math.random() * .075;

    const gradient = ctx.createRadialGradient(
      0, 0, 0,
      0, 0, r
    );

    gradient.addColorStop(
      0,
      `rgba(100,60,160,${alpha.toFixed(3)})`
    );

    gradient.addColorStop(
      .5,
      `rgba(65,30,115,${(alpha * .38).toFixed(3)})`
    );

    gradient.addColorStop(
      1,
      'rgba(0,0,0,0)'
    );

    return {
      x: -r + Math.random() * (W + r * 2),

      y: rand
        ? H * .48 + Math.random() * H * .50
        : H * .60 + Math.random() * H * .38,

      r,
      alpha,
      spd: .10 + Math.random() * .24,
      drift: (Math.random() - .5) * .04,
      gradient
    };
  }

  // CANDLES
  function buildCandles() {
    candles = [];

    // Place candles around the gravestones.
    // More candles are placed around the foreground/middle rows.
    gravestones.forEach((gs, index) => {
      if (index % 2 === 0 || gs.row === 2) {
        const offsetX =
          (Math.random() - .5) * gs.w * 1.8;

        candles.push({
          x: gs.x + offsetX,
          y: gs.y + 4,
          size: 2.5 + Math.random() * 2.5,

          phase: Math.random() * Math.PI * 2,
          flickerSpeed: .08 + Math.random() * .08,

          baseAlpha: .55 + Math.random() * .25,

          // Slightly behind/in front depending on the gravestone row
          row: gs.row
        });
      }
    });

    // Add a few extra candles where needed
    while (candles.length < CANDLE_COUNT) {
      const gs =
        gravestones[Math.floor(Math.random() * gravestones.length)];

      candles.push({
        x: gs.x + (Math.random() - .5) * gs.w * 2,
        y: gs.y + 3 + Math.random() * 5,
        size: 2.5 + Math.random() * 2.5,

        phase: Math.random() * Math.PI * 2,
        flickerSpeed: .08 + Math.random() * .08,

        baseAlpha: .55 + Math.random() * .25,

        row: gs.row
      });
    }
  }

  // GROUND LAYER
  function buildGroundLayer() {
    const g = groundCtx;

    g.clearRect(0, 0, W, H);

    const fill = g.createLinearGradient(
      0,
      H * .50,
      0,
      H
    );

    fill.addColorStop(0, '#0C0818');
    fill.addColorStop(.18, '#0F0A1E');
    fill.addColorStop(.55, '#0A061A');
    fill.addColorStop(1, '#080514');

    g.fillStyle = fill;
    g.fillRect(
      0,
      H * .50,
      W,
      H * .50
    );

    const blend = g.createLinearGradient(
      0,
      H * .46,
      0,
      H * .56
    );

    blend.addColorStop(
      0,
      'rgba(12,8,24,0)'
    );

    blend.addColorStop(
      1,
      'rgba(12,8,24,1)'
    );

    g.fillStyle = blend;

    g.fillRect(
      0,
      H * .46,
      W,
      H * .10
    );

    // Grass
    g.fillStyle = '#0C071A';

    for (let i = 0; i < W; i += 5) {
      const gh =
        3 +
        Math.sin(i * .25) * 2.8 +
        Math.cos(i * .13) * 2;

      g.fillRect(
        i,
        H * .50 - gh,
        2,
        gh + 1
      );
    }

    // Gravestone shadows
    gravestones.forEach(gs => {
      g.save();

      g.translate(
        gs.x,
        gs.y + 1
      );

      const mw = gs.w * 2.0;

      const mg = g.createRadialGradient(
        0,
        0,
        0,
        0,
        0,
        mw * .5
      );

      mg.addColorStop(
        0,
        `rgba(15,8,28,${.45 + gs.row * .12})`
      );

      mg.addColorStop(
        1,
        'rgba(0,0,0,0)'
      );

      g.scale(1, .28);

      g.beginPath();

      g.arc(
        0,
        0,
        mw * .5,
        0,
        Math.PI * 2
      );

      g.fillStyle = mg;
      g.fill();

      g.restore();
    });

    // Gravestones
    gravestones.forEach(gs => {
      g.save();

      g.translate(
        gs.x,
        gs.y
      );

      g.rotate(gs.lean);

      const col =
        `rgba(8,4,16,${.72 + gs.row * .12})`;

      const rim =
        `rgba(115,75,185,${.07 + gs.row * .05})`;

      if (gs.type === 'arch') {
        const rx = gs.w / 2;

        g.fillStyle = col;

        g.beginPath();

        g.moveTo(
          -rx,
          0
        );

        g.lineTo(
          -rx,
          -gs.h * .52
        );

        g.arc(
          0,
          -gs.h * .52,
          rx,
          Math.PI,
          0
        );

        g.lineTo(
          rx,
          0
        );

        g.closePath();

        g.fill();

        g.strokeStyle = rim;
        g.lineWidth = .7;
        g.stroke();

        if (gs.detail === 0) {
          g.beginPath();

          g.ellipse(
            0,
            -gs.h * .66,
            rx * .38,
            rx * .46,
            0,
            0,
            Math.PI * 2
          );

          g.strokeStyle = rim;
          g.lineWidth = .5;
          g.stroke();

        } else if (gs.detail === 1) {
          g.beginPath();

          g.moveTo(
            -rx * .58,
            -gs.h * .32
          );

          g.lineTo(
            rx * .58,
            -gs.h * .32
          );

          g.strokeStyle = rim;
          g.lineWidth = .5;
          g.stroke();
        }

      } else {
        g.fillStyle = col;

        g.beginPath();

        g.moveTo(
          -gs.w * .40,
          0
        );

        g.lineTo(
          -gs.w * .48,
          -gs.h * .28
        );

        g.lineTo(
          -gs.w * .36,
          -gs.h
        );

        g.lineTo(
          gs.w * .36,
          -gs.h
        );

        g.lineTo(
          gs.w * .48,
          -gs.h * .28
        );

        g.lineTo(
          gs.w * .40,
          0
        );

        g.closePath();

        g.fill();

        g.strokeStyle = rim;
        g.lineWidth = .7;
        g.stroke();

        if (gs.detail === 1) {
          g.beginPath();

          g.moveTo(
            -gs.w * .28,
            -gs.h * .62
          );

          g.lineTo(
            gs.w * .28,
            -gs.h * .62
          );

          g.strokeStyle = rim;
          g.lineWidth = .5;
          g.stroke();
        }
      }

      g.restore();
    });
  }

  // SKY + MOON
  function buildSkyAndMoon() {
    skyGradient =
      ctx.createLinearGradient(
        0,
        0,
        0,
        H * .55
      );

    skyGradient.addColorStop(
      0,
      '#050210'
    );

    skyGradient.addColorStop(
      .5,
      '#090517'
    );

    skyGradient.addColorStop(
      1,
      '#0E071E'
    );

    moonX = W * .25;
    moonY = H * .18;

    moonR =
      Math.min(W, H) * .08;

    moonCorona =
      ctx.createRadialGradient(
        0,
        0,
        moonR * .65,
        0,
        0,
        moonR * 3.4
      );

    moonCorona.addColorStop(
      0,
      'rgba(205,185,255,.24)'
    );

    moonCorona.addColorStop(
      .3,
      'rgba(165,135,240,.09)'
    );

    moonCorona.addColorStop(
      .7,
      'rgba(120,85,195,.03)'
    );

    moonCorona.addColorStop(
      1,
      'rgba(0,0,0,0)'
    );

    moonBody =
      ctx.createRadialGradient(
        -moonR * .28,
        -moonR * .22,
        moonR * .04,
        0,
        0,
        moonR
      );

    moonBody.addColorStop(
      0,
      '#EDE5FF'
    );

    moonBody.addColorStop(
      .45,
      '#D0C0F0'
    );

    moonBody.addColorStop(
      .85,
      '#B0A0E0'
    );

    moonBody.addColorStop(
      1,
      '#8870B8'
    );

    moonMist =
      ctx.createRadialGradient(
        0,
        0,
        moonR * .15,
        0,
        0,
        moonR * 1.05
      );

    moonMist.addColorStop(
      0,
      'rgba(140,120,195,0)'
    );

    moonMist.addColorStop(
      .55,
      'rgba(140,120,195,.16)'
    );

    moonMist.addColorStop(
      1,
      'rgba(140,120,195,0)'
    );
  }

  // DRAW SKY
  function drawSky() {
    ctx.fillStyle = skyGradient;

    ctx.fillRect(
      0,
      0,
      W,
      H * .55
    );
  }

  // DRAW MOON
  function drawMoon() {
    const pulse =
      1 +
      .04 *
      Math.sin(tick * .018);

    ctx.save();

    ctx.translate(
      moonX,
      moonY
    );

    ctx.save();

    ctx.scale(
      pulse,
      pulse
    );

    ctx.beginPath();

    ctx.arc(
      0,
      0,
      moonR * 3.4,
      0,
      Math.PI * 2
    );

    ctx.fillStyle = moonCorona;
    ctx.fill();

    ctx.restore();

    ctx.beginPath();

    ctx.arc(
      0,
      0,
      moonR,
      0,
      Math.PI * 2
    );

    ctx.fillStyle = moonBody;
    ctx.fill();

    ctx.globalAlpha =
      .55 +
      .45 *
      Math.sin(tick * .012);

    ctx.beginPath();

    ctx.arc(
      0,
      0,
      moonR * 1.05,
      0,
      Math.PI * 2
    );

    ctx.fillStyle = moonMist;
    ctx.fill();

    ctx.globalAlpha = 1;

    ctx.restore();
  }

  // DRAW STARS
  function drawStars() {
    stars.forEach(s => {
      const a =
        s.base +
        .3 *
        Math.sin(
          tick * s.spd +
          s.phase
        );

      ctx.beginPath();

      ctx.arc(
        s.x,
        s.y,
        s.r,
        0,
        Math.PI * 2
      );

      ctx.fillStyle =
        `rgba(210,190,255,${a.toFixed(2)})`;

      ctx.fill();
    });
  }

  // DRAW GROUND
  function drawGround() {
    ctx.drawImage(
      groundCanvas,
      0,
      0
    );
  }

  // DRAW FOG
  function drawFog(mult) {
    fogParticles.forEach(f => {
      f.x += f.spd;
      f.y += f.drift;

      if (f.x - f.r > W + 40) {
        const fresh = makeFog();

        fresh.x = -fresh.r;

        Object.assign(
          f,
          fresh
        );
      }

      ctx.save();

      ctx.translate(
        f.x,
        f.y
      );

      ctx.globalAlpha = mult;

      ctx.beginPath();

      ctx.arc(
        0,
        0,
        f.r,
        0,
        Math.PI * 2
      );

      ctx.fillStyle = f.gradient;

      ctx.fill();

      ctx.restore();
    });
  }

  // DRAW CANDLES
  function drawCandles() {
    candles.forEach(c => {
      const flicker =
        Math.sin(
          tick * c.flickerSpeed +
          c.phase
        );

      const flicker2 =
        Math.sin(
          tick * c.flickerSpeed * 1.7 +
          c.phase * 2
        );

      const flameHeight =
        c.size *
        (1.7 + flicker * .25 + flicker2 * .12);

      const flameWidth =
        c.size *
        (.65 + flicker * .08);

      const glowStrength =
        .55 +
        flicker * .12;

      // Candle glow
      const glow =
        ctx.createRadialGradient(
          c.x,
          c.y - c.size * 1.5,
          0,
          c.x,
          c.y - c.size * 1.5,
          c.size * 9
        );

      glow.addColorStop(
        0,
        `rgba(255,190,80,${(.20 * glowStrength).toFixed(3)})`
      );

      glow.addColorStop(
        .35,
        `rgba(255,150,45,${(.08 * glowStrength).toFixed(3)})`
      );

      glow.addColorStop(
        1,
        'rgba(255,120,20,0)'
      );

      ctx.save();

      ctx.globalCompositeOperation =
        'screen';

      ctx.fillStyle = glow;

      ctx.beginPath();

      ctx.arc(
        c.x,
        c.y - c.size * 1.5,
        c.size * 9,
        0,
        Math.PI * 2
      );

      ctx.fill();

      ctx.restore();

      // Candle body
      ctx.save();

      ctx.globalAlpha =
        c.baseAlpha;

      ctx.fillStyle =
        '#B8A48C';

      ctx.fillRect(
        c.x - c.size * .38,
        c.y - c.size * .35,
        c.size * .76,
        c.size * 1.5
      );

      // Candle top
      ctx.beginPath();

      ctx.ellipse(
        c.x,
        c.y - c.size * .35,
        c.size * .38,
        c.size * .12,
        0,
        0,
        Math.PI * 2
      );

      ctx.fillStyle =
        '#D4C4AA';

      ctx.fill();

      // Flame
      ctx.globalAlpha = 1;

      const flameGradient =
        ctx.createRadialGradient(
          c.x,
          c.y - c.size * 1.45,
          0,
          c.x,
          c.y - c.size * 1.45,
          flameHeight
        );

      flameGradient.addColorStop(
        0,
        'rgba(255,245,190,1)'
      );

      flameGradient.addColorStop(
        .35,
        'rgba(255,190,70,.95)'
      );

      flameGradient.addColorStop(
        .75,
        'rgba(255,100,25,.65)'
      );

      flameGradient.addColorStop(
        1,
        'rgba(255,70,0,0)'
      );

      ctx.fillStyle =
        flameGradient;

      ctx.beginPath();

      ctx.moveTo(
        c.x,
        c.y - c.size * .55
      );

      ctx.bezierCurveTo(
        c.x - flameWidth,
        c.y - c.size * 1.1,
        c.x - flameWidth * .55,
        c.y - flameHeight,
        c.x,
        c.y - flameHeight
      );

      ctx.bezierCurveTo(
        c.x + flameWidth * .65,
        c.y - flameHeight * .75,
        c.x + flameWidth,
        c.y - c.size * 1.05,
        c.x,
        c.y - c.size * .55
      );

      ctx.fill();

      ctx.restore();
    });
  }

  // LIGHTNING
  function drawLightning() {
    const ls = lightning;

    ls.timer++;

    if (
      !ls.active &&
      ls.timer >= ls.nextFlash
    ) {
      ls.active = true;
      ls.alpha = 1;
      ls.timer = 0;

      ls.nextFlash =
        260 +
        Math.random() * 400;
    }

    if (ls.active) {
      ctx.fillStyle =
        `rgba(185,145,255,${(
          ls.alpha * .13
        ).toFixed(3)})`;

      ctx.fillRect(
        0,
        0,
        W,
        H
      );

      if (ls.alpha > .65) {
        const bx =
          W * .15 +
          Math.random() * W * .70;

        ctx.save();

        ctx.strokeStyle =
          `rgba(215,185,255,${ls.alpha.toFixed(2)})`;

        ctx.lineWidth =
          1.5 +
          ls.alpha;

        ctx.shadowBlur = 8;
        ctx.shadowColor =
          '#A060FF';

        ctx.beginPath();

        ctx.moveTo(
          bx,
          0
        );

        let cy = 0;
        let cx = bx;

        while (cy < H * .58) {
          cx +=
            (Math.random() - .5) * 42;

          cy +=
            15 +
            Math.random() * 20;

          ctx.lineTo(
            cx,
            cy
          );
        }

        ctx.stroke();

        if (ls.alpha > .82) {
          ctx.beginPath();

          ctx.moveTo(
            cx,
            cy * .55
          );

          let bcy = cy * .55;
          let bcx = cx;

          for (let b = 0; b < 4; b++) {
            bcx +=
              (Math.random() - .5) * 28;

            bcy +=
              12 +
              Math.random() * 14;

            ctx.lineTo(
              bcx,
              bcy
            );
          }

          ctx.lineWidth = .8;
          ctx.stroke();
        }

        ctx.restore();
      }

      ls.alpha -= .055;

      if (ls.alpha <= 0) {
        ls.active = false;
      }
    }
  }

  // MAIN DRAW
  function draw(ts) {
    if (!W || !H) return;

    if (
      ts - lastFrame <
      FRAME_MS
    ) {
      return;
    }

    lastFrame = ts;
    tick++;

    ctx.clearRect(
      0,
      0,
      W,
      H
    );

    drawSky();
    drawMoon();
    drawStars();

    drawFog(.4);

    drawGround();

    // Candles are drawn after the ground
    // so their glow and flames remain visible.
    drawCandles();

    drawFog(.85);

    drawLightning();
  }

  function loop(ts) {
    draw(ts);

    animId =
      requestAnimationFrame(loop);
  }

  function destroy() {
    if (animId) {
      cancelAnimationFrame(animId);
    }

    animId = null;

    gravestones = [];
    fogParticles = [];
    candles = [];
    stars = [];
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

  animId =
    requestAnimationFrame(loop);

  return {
    destroy,
    resize
  };
}