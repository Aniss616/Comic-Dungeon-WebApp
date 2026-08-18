let animId = null;
let W = 0, H = 0;
let lastFrame = 0;
const FRAME_MS = 1000 / 30;

let gravestones = [];
let fogParticles = [];
let stars = [];
let candles = [];

// Lightning:
// First flash = 5 seconds
// Then every 30 seconds
let lightning = {
  active: false,
  alpha: 0,
  lastFlash: -25000,
  flashInterval: 30000
};

let tick = 0;

// Offscreen cache for static ground layer
let groundCanvas = null;
let groundCtx = null;

// Cached gradients
let skyGradient = null;
let moonCorona = null;
let moonBody = null;
let moonMist = null;

let moonX = 0;
let moonY = 0;
let moonR = 0;

// Tunable counts
const GRAVESTONE_COUNTS = {
  back: 10,
  mid: 8,
  front: 6
};

const FOG_COUNT = 14;
const STAR_COUNT = 40;
const CANDLE_COUNT = 18;

export function initSupernatural(canvas) {
  const ctx = canvas.getContext('2d', {
    alpha: true
  });

  // ============================================================
  // RESIZE
  // ============================================================

  function resize() {
    W = canvas.offsetWidth;
    H = canvas.offsetHeight;

    canvas.width = W;
    canvas.height = H;

    if (!groundCanvas) {
      groundCanvas = document.createElement('canvas');

      groundCtx =
        groundCanvas.getContext('2d', {
          alpha: true
        });
    }

    groundCanvas.width = W;
    groundCanvas.height = H;

    build();
  }

  // ============================================================
  // BUILD
  // ============================================================

  function build() {
    buildStars();
    buildGravestones();
    buildFog();
    buildCandles();
    buildGroundLayer();
    buildSkyAndMoon();
  }

  // ============================================================
  // STARS
  // ============================================================

  function buildStars() {
    stars = [];

    const mobile = W <= 600;

    /*
     * Mobile gets a tighter grid so the stars are spread
     * properly across the narrow viewport.
     */
    const cols = mobile ? 7 : 10;

    const usableHeight = H * 0.43;

    const rowCount =
      Math.ceil(
        STAR_COUNT / cols
      );

    for (
      let i = 0;
      i < STAR_COUNT;
      i++
    ) {
      const col = i % cols;

      const row =
        Math.floor(
          i / cols
        );

      const cellW =
        W / cols;

      const cellH =
        usableHeight /
        rowCount;

      /*
       * Deterministic offsets keep the distribution natural
       * without creating large empty areas on mobile.
       */
      const offsetX =
        Math.sin(
          i * 12.9898
        ) *
        cellW *
        0.28;

      const offsetY =
        Math.cos(
          i * 78.233
        ) *
        cellH *
        0.28;

      let x =
        col * cellW +
        cellW * 0.5 +
        offsetX;

      let y =
        row * cellH +
        cellH * 0.5 +
        offsetY;

      // Keep stars inside the canvas
      x = Math.max(
        3,
        Math.min(
          W - 3,
          x
        )
      );

      y = Math.max(
        3,
        Math.min(
          usableHeight,
          y
        )
      );

      stars.push({
        x,
        y,

        r: mobile
          ? 0.45 +
            (i % 3) *
              0.30
          : 0.5 +
            (i % 3) *
              0.35,

        phase:
          Math.random() *
          Math.PI *
          2,

        spd:
          0.02 +
          Math.random() *
            0.03,

        base:
          0.32 +
          Math.random() *
            0.3
      });
    }
  }

  // ============================================================
  // GRAVESTONES
  // ============================================================

  function buildGravestones() {
    gravestones = [];

    const rows = [
      {
        n: GRAVESTONE_COUNTS.back,
        yFrac: 0.535,
        xPad: 0.01,
        xSpread: 0.98,
        wr: [12, 22],
        hr: [18, 40],
        lean: 0.20,
        archChance: 0.5,
        row: 0
      },

      {
        n: GRAVESTONE_COUNTS.mid,
        yFrac: 0.635,
        xPad: 0.03,
        xSpread: 0.94,
        wr: [16, 29],
        hr: [24, 52],
        lean: 0.18,
        archChance: 0.55,
        row: 1
      },

      {
        n: GRAVESTONE_COUNTS.front,
        yFrac: 0.735,
        xPad: 0.05,
        xSpread: 0.90,
        wr: [20, 36],
        hr: [30, 64],
        lean: 0.15,
        archChance: 0.6,
        row: 2
      }
    ];

    rows.forEach(cfg => {
      for (
        let i = 0;
        i < cfg.n;
        i++
      ) {
        const t =
          cfg.n > 1
            ? i / (cfg.n - 1)
            : 0;

        gravestones.push({
          x:
            W * cfg.xPad +
            t *
              W *
              cfg.xSpread +
            (Math.random() -
              0.5) *
              26,

          y:
            H *
              cfg.yFrac +
            (Math.random() -
              0.5) *
              10,

          w:
            cfg.wr[0] +
            Math.random() *
              (
                cfg.wr[1] -
                cfg.wr[0]
              ),

          h:
            cfg.hr[0] +
            Math.random() *
              (
                cfg.hr[1] -
                cfg.hr[0]
              ),

          lean:
            (Math.random() -
              0.5) *
            cfg.lean,

          row: cfg.row,

          type:
            Math.random() <
            cfg.archChance
              ? 'arch'
              : 'rect',

          detail:
            Math.floor(
              Math.random() * 3
            )
        });
      }
    });

    gravestones.sort(
      (a, b) =>
        a.y - b.y
    );
  }

  // ============================================================
  // FOG
  // ============================================================

  function buildFog() {
    fogParticles = [];

    for (
      let i = 0;
      i < FOG_COUNT;
      i++
    ) {
      fogParticles.push(
        makeFog(true)
      );
    }
  }

  function makeFog(rand = false) {
    const r =
      90 +
      Math.random() *
        170;

    const alpha =
      0.032 +
      Math.random() *
        0.075;

    const gradient =
      ctx.createRadialGradient(
        0,
        0,
        0,
        0,
        0,
        r
      );

    gradient.addColorStop(
      0,
      `rgba(100,60,160,${alpha.toFixed(3)})`
    );

    gradient.addColorStop(
      0.5,
      `rgba(65,30,115,${(
        alpha * 0.38
      ).toFixed(3)})`
    );

    gradient.addColorStop(
      1,
      'rgba(0,0,0,0)'
    );

    return {
      x:
        -r +
        Math.random() *
          (W + r * 2),

      y: rand
        ? H * 0.48 +
          Math.random() *
            H * 0.50
        : H * 0.60 +
          Math.random() *
            H * 0.38,

      r,

      alpha,

      spd:
        0.10 +
        Math.random() *
          0.24,

      drift:
        (Math.random() -
          0.5) *
        0.04,

      gradient
    };
  }

  // ============================================================
  // CANDLES
  // ============================================================

  function buildCandles() {
    candles = [];

    gravestones.forEach(
      (gs, index) => {
        if (
          index % 2 === 0 ||
          gs.row === 2
        ) {
          const offsetX =
            (Math.random() -
              0.5) *
            gs.w *
            1.8;

          candles.push({
            x:
              gs.x +
              offsetX,

            y:
              gs.y + 4,

            size:
              2.5 +
              Math.random() *
                2.5,

            phase:
              Math.random() *
              Math.PI *
              2,

            flickerSpeed:
              0.08 +
              Math.random() *
                0.08,

            baseAlpha:
              0.55 +
              Math.random() *
                0.25,

            row:
              gs.row
          });
        }
      }
    );

    while (
      candles.length <
      CANDLE_COUNT
    ) {
      const gs =
        gravestones[
          Math.floor(
            Math.random() *
              gravestones.length
          )
        ];

      candles.push({
        x:
          gs.x +
          (Math.random() -
            0.5) *
            gs.w *
            2,

        y:
          gs.y +
          3 +
          Math.random() *
            5,

        size:
          2.5 +
          Math.random() *
            2.5,

        phase:
          Math.random() *
          Math.PI *
          2,

        flickerSpeed:
          0.08 +
          Math.random() *
            0.08,

        baseAlpha:
          0.55 +
          Math.random() *
            0.25,

        row:
          gs.row
      });
    }
  }

  // ============================================================
  // GROUND LAYER
  // ============================================================

  function buildGroundLayer() {
    const g = groundCtx;

    g.clearRect(
      0,
      0,
      W,
      H
    );

    const fill =
      g.createLinearGradient(
        0,
        H * 0.50,
        0,
        H
      );

    fill.addColorStop(
      0,
      '#0C0818'
    );

    fill.addColorStop(
      0.18,
      '#0F0A1E'
    );

    fill.addColorStop(
      0.55,
      '#0A061A'
    );

    fill.addColorStop(
      1,
      '#080514'
    );

    g.fillStyle = fill;

    g.fillRect(
      0,
      H * 0.50,
      W,
      H * 0.50
    );

    const blend =
      g.createLinearGradient(
        0,
        H * 0.46,
        0,
        H * 0.56
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
      H * 0.46,
      W,
      H * 0.10
    );

    // Grass
    g.fillStyle =
      '#0C071A';

    for (
      let i = 0;
      i < W;
      i += 5
    ) {
      const gh =
        3 +
        Math.sin(
          i * 0.25
        ) *
          2.8 +
        Math.cos(
          i * 0.13
        ) *
          2;

      g.fillRect(
        i,
        H * 0.50 -
          gh,
        2,
        gh + 1
      );
    }

    // Gravestone shadows
    gravestones.forEach(
      gs => {
        g.save();

        g.translate(
          gs.x,
          gs.y + 1
        );

        const mw =
          gs.w * 2.0;

        const mg =
          g.createRadialGradient(
            0,
            0,
            0,
            0,
            0,
            mw * 0.5
          );

        mg.addColorStop(
          0,
          `rgba(15,8,28,${
            0.45 +
            gs.row *
              0.12
          })`
        );

        mg.addColorStop(
          1,
          'rgba(0,0,0,0)'
        );

        g.scale(
          1,
          0.28
        );

        g.beginPath();

        g.arc(
          0,
          0,
          mw * 0.5,
          0,
          Math.PI * 2
        );

        g.fillStyle = mg;

        g.fill();

        g.restore();
      }
    );

    // Gravestones
    gravestones.forEach(
      gs => {
        g.save();

        g.translate(
          gs.x,
          gs.y
        );

        g.rotate(
          gs.lean
        );

        const col =
          `rgba(8,4,16,${
            0.72 +
            gs.row *
              0.12
          })`;

        const rim =
          `rgba(115,75,185,${
            0.07 +
            gs.row *
              0.05
          })`;

        if (
          gs.type === 'arch'
        ) {
          const rx =
            gs.w / 2;

          g.fillStyle = col;

          g.beginPath();

          g.moveTo(
            -rx,
            0
          );

          g.lineTo(
            -rx,
            -gs.h *
              0.52
          );

          g.arc(
            0,
            -gs.h *
              0.52,
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

          g.strokeStyle =
            rim;

          g.lineWidth =
            0.7;

          g.stroke();

          if (
            gs.detail === 0
          ) {
            g.beginPath();

            g.ellipse(
              0,
              -gs.h *
                0.66,
              rx * 0.38,
              rx * 0.46,
              0,
              0,
              Math.PI * 2
            );

            g.strokeStyle =
              rim;

            g.lineWidth =
              0.5;

            g.stroke();

          } else if (
            gs.detail === 1
          ) {
            g.beginPath();

            g.moveTo(
              -rx * 0.58,
              -gs.h *
                0.32
            );

            g.lineTo(
              rx * 0.58,
              -gs.h *
                0.32
            );

            g.strokeStyle =
              rim;

            g.lineWidth =
              0.5;

            g.stroke();
          }

        } else {
          g.fillStyle = col;

          g.beginPath();

          g.moveTo(
            -gs.w * 0.40,
            0
          );

          g.lineTo(
            -gs.w * 0.48,
            -gs.h * 0.28
          );

          g.lineTo(
            -gs.w * 0.36,
            -gs.h
          );

          g.lineTo(
            gs.w * 0.36,
            -gs.h
          );

          g.lineTo(
            gs.w * 0.48,
            -gs.h * 0.28
          );

          g.lineTo(
            gs.w * 0.40,
            0
          );

          g.closePath();

          g.fill();

          g.strokeStyle =
            rim;

          g.lineWidth =
            0.7;

          g.stroke();

          if (
            gs.detail === 1
          ) {
            g.beginPath();

            g.moveTo(
              -gs.w * 0.28,
              -gs.h *
                0.62
            );

            g.lineTo(
              gs.w * 0.28,
              -gs.h *
                0.62
            );

            g.strokeStyle =
              rim;

            g.lineWidth =
              0.5;

            g.stroke();
          }
        }

        g.restore();
      }
    );
  }

  // ============================================================
  // SKY + MOON
  // ============================================================

  function buildSkyAndMoon() {
    skyGradient =
      ctx.createLinearGradient(
        0,
        0,
        0,
        H * 0.55
      );

    skyGradient.addColorStop(
      0,
      '#050210'
    );

    skyGradient.addColorStop(
      0.5,
      '#090517'
    );

    skyGradient.addColorStop(
      1,
      '#0E071E'
    );

    // Slightly further left on mobile
    moonX =
      W <= 600
        ? W * 0.23
        : W * 0.25;

    moonY =
      H * 0.18;

    moonR =
      Math.min(
        W,
        H
      ) * 0.08;

    moonCorona =
      ctx.createRadialGradient(
        0,
        0,
        moonR * 0.65,
        0,
        0,
        moonR * 3.4
      );

    moonCorona.addColorStop(
      0,
      'rgba(205,185,255,.24)'
    );

    moonCorona.addColorStop(
      0.3,
      'rgba(165,135,240,.09)'
    );

    moonCorona.addColorStop(
      0.7,
      'rgba(120,85,195,.03)'
    );

    moonCorona.addColorStop(
      1,
      'rgba(0,0,0,0)'
    );

    moonBody =
      ctx.createRadialGradient(
        -moonR * 0.28,
        -moonR * 0.22,
        moonR * 0.04,
        0,
        0,
        moonR
      );

    moonBody.addColorStop(
      0,
      '#EDE5FF'
    );

    moonBody.addColorStop(
      0.45,
      '#D0C0F0'
    );

    moonBody.addColorStop(
      0.85,
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
        moonR * 0.15,
        0,
        0,
        moonR * 1.05
      );

    moonMist.addColorStop(
      0,
      'rgba(140,120,195,0)'
    );

    moonMist.addColorStop(
      0.55,
      'rgba(140,120,195,.16)'
    );

    moonMist.addColorStop(
      1,
      'rgba(140,120,195,0)'
    );
  }

  // ============================================================
  // DRAW SKY
  // ============================================================

  function drawSky() {
    ctx.fillStyle =
      skyGradient;

    ctx.fillRect(
      0,
      0,
      W,
      H * 0.55
    );
  }

  // ============================================================
  // DRAW MOON
  // ============================================================

  function drawMoon() {
    const pulse =
      1 +
      0.04 *
        Math.sin(
          tick * 0.018
        );

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

    ctx.fillStyle =
      moonCorona;

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

    ctx.fillStyle =
      moonBody;

    ctx.fill();

    ctx.globalAlpha =
      0.55 +
      0.45 *
        Math.sin(
          tick * 0.012
        );

    ctx.beginPath();

    ctx.arc(
      0,
      0,
      moonR * 1.05,
      0,
      Math.PI * 2
    );

    ctx.fillStyle =
      moonMist;

    ctx.fill();

    ctx.globalAlpha = 1;

    ctx.restore();
  }

  // ============================================================
  // DRAW STARS
  // ============================================================

  function drawStars() {
    stars.forEach(s => {
      const a =
        s.base +
        0.3 *
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

  // ============================================================
  // DRAW GROUND
  // ============================================================

  function drawGround() {
    ctx.drawImage(
      groundCanvas,
      0,
      0
    );
  }

  // ============================================================
  // DRAW FOG
  // ============================================================

  function drawFog(mult) {
    fogParticles.forEach(f => {
      f.x += f.spd;
      f.y += f.drift;

      if (
        f.x - f.r >
        W + 40
      ) {
        const fresh =
          makeFog();

        fresh.x =
          -fresh.r;

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

      ctx.globalAlpha =
        mult;

      ctx.beginPath();

      ctx.arc(
        0,
        0,
        f.r,
        0,
        Math.PI * 2
      );

      ctx.fillStyle =
        f.gradient;

      ctx.fill();

      ctx.restore();
    });
  }

  // ============================================================
  // DRAW CANDLES
  // ============================================================

  function drawCandles() {
    candles.forEach(c => {

      const flicker =
        Math.sin(
          tick *
            c.flickerSpeed +
            c.phase
        );

      const flicker2 =
        Math.sin(
          tick *
            c.flickerSpeed *
            1.7 +
            c.phase * 2
        );

      const flameHeight =
        c.size *
        (
          1.7 +
          flicker * 0.25 +
          flicker2 * 0.12
        );

      const flameWidth =
        c.size *
        (
          0.65 +
          flicker * 0.08
        );

      const glowStrength =
        0.55 +
        flicker * 0.12;

      // Candle glow
      const glow =
        ctx.createRadialGradient(
          c.x,
          c.y -
            c.size * 1.5,
          0,
          c.x,
          c.y -
            c.size * 1.5,
          c.size * 9
        );

      glow.addColorStop(
        0,
        `rgba(255,190,80,${
          (
            0.20 *
            glowStrength
          ).toFixed(3)
        })`
      );

      glow.addColorStop(
        0.35,
        `rgba(255,150,45,${
          (
            0.08 *
            glowStrength
          ).toFixed(3)
        })`
      );

      glow.addColorStop(
        1,
        'rgba(255,120,20,0)'
      );

      ctx.save();

      ctx.globalCompositeOperation =
        'screen';

      ctx.fillStyle =
        glow;

      ctx.beginPath();

      ctx.arc(
        c.x,
        c.y -
          c.size * 1.5,
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
        c.x -
          c.size * 0.38,
        c.y -
          c.size * 0.35,
        c.size * 0.76,
        c.size * 1.5
      );

      // Candle top
      ctx.beginPath();

      ctx.ellipse(
        c.x,
        c.y -
          c.size * 0.35,
        c.size * 0.38,
        c.size * 0.12,
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
          c.y -
            c.size * 1.45,
          0,
          c.x,
          c.y -
            c.size * 1.45,
          flameHeight
        );

      flameGradient.addColorStop(
        0,
        'rgba(255,245,190,1)'
      );

      flameGradient.addColorStop(
        0.35,
        'rgba(255,190,70,.95)'
      );

      flameGradient.addColorStop(
        0.75,
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
        c.y -
          c.size * 0.55
      );

      ctx.bezierCurveTo(
        c.x -
          flameWidth,
        c.y -
          c.size * 1.1,
        c.x -
          flameWidth * 0.55,
        c.y -
          flameHeight,
        c.x,
        c.y -
          flameHeight
      );

      ctx.bezierCurveTo(
        c.x +
          flameWidth * 0.65,
        c.y -
          flameHeight * 0.75,
        c.x +
          flameWidth,
        c.y -
          c.size * 1.05,
        c.x,
        c.y -
          c.size * 0.55
      );

      ctx.fill();

      ctx.restore();
    });
  }

  // ============================================================
  // LIGHTNING
  // ============================================================

  function drawLightning(ts) {
    const ls = lightning;

    /*
     * ts is the timestamp supplied by requestAnimationFrame.
     *
     * With the recording script's virtual clock:
     * 0ms -> beginning
     * 5000ms -> first flash
     * 35000ms -> second flash
     * 65000ms -> third flash
     *
     * This is independent of FPS.
     */

    if (
      !ls.active &&
      ts - ls.lastFlash >=
        ls.flashInterval
    ) {
      ls.active = true;

      ls.alpha = 1;

      ls.lastFlash = ts;
    }

    if (!ls.active) {
      return;
    }

    // ----------------------------------------------------------
    // AMBIENT PURPLE FLASH
    // ----------------------------------------------------------

    ctx.save();

    ctx.fillStyle =
      `rgba(185,145,255,${
        (
          ls.alpha * 0.16
        ).toFixed(3)
      })`;

    ctx.fillRect(
      0,
      0,
      W,
      H
    );

    // ----------------------------------------------------------
    // LIGHTNING BOLT
    // ----------------------------------------------------------

    if (
      ls.alpha > 0.05
    ) {

      const mobile =
        W <= 600;

      const bx =
        W * 0.15 +
        Math.random() *
          W * 0.70;

      ctx.strokeStyle =
        `rgba(225,205,255,${
          ls.alpha.toFixed(2)
        })`;

      ctx.lineWidth =
        1.5 +
        ls.alpha * 1.5;

      ctx.shadowBlur =
        mobile
          ? 7
          : 10;

      ctx.shadowColor =
        '#A060FF';

      ctx.beginPath();

      ctx.moveTo(
        bx,
        0
      );

      let cx = bx;
      let cy = 0;

      while (
        cy < H * 0.58
      ) {

        cx +=
          (
            Math.random() -
            0.5
          ) *
          (
            mobile
              ? 24
              : 42
          );

        cy +=
          15 +
          Math.random() *
            20;

        ctx.lineTo(
          cx,
          cy
        );
      }

      ctx.stroke();

      // --------------------------------------------------------
      // SECONDARY BRANCH
      // --------------------------------------------------------

      if (
        ls.alpha > 0.65
      ) {

        ctx.beginPath();

        ctx.moveTo(
          cx,
          cy * 0.55
        );

        let branchX = cx;

        let branchY =
          cy * 0.55;

        for (
          let i = 0;
          i < 4;
          i++
        ) {

          branchX +=
            (
              Math.random() -
              0.5
            ) *
            (
              mobile
                ? 18
                : 28
            );

          branchY +=
            12 +
            Math.random() *
              14;

          ctx.lineTo(
            branchX,
            branchY
          );
        }

        ctx.lineWidth =
          0.8;

        ctx.stroke();
      }
    }

    ctx.restore();

    // ----------------------------------------------------------
    // FADE
    // ----------------------------------------------------------

    ls.alpha -= 0.055;

    if (
      ls.alpha <= 0
    ) {
      ls.alpha = 0;
      ls.active = false;
    }
  }

  // ============================================================
  // MAIN DRAW
  // ============================================================

  function draw(ts) {

    if (
      !W ||
      !H
    ) {
      return;
    }

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

    drawFog(0.4);

    drawGround();

    // Candles are drawn after ground
    // so their glow remains visible.
    drawCandles();

    drawFog(0.85);

    // IMPORTANT:
    // Pass the animation timestamp.
    drawLightning(ts);
  }

  // ============================================================
  // ANIMATION LOOP
  // ============================================================

  function loop(ts) {
    draw(ts);

    animId =
      requestAnimationFrame(
        loop
      );
  }

  // ============================================================
  // DESTROY
  // ============================================================

  function destroy() {

    if (animId) {
      cancelAnimationFrame(
        animId
      );
    }

    animId = null;

    gravestones = [];
    fogParticles = [];
    candles = [];
    stars = [];
  }

  // ============================================================
  // VISIBILITY
  // ============================================================

  document.addEventListener(
    'visibilitychange',
    () => {

      if (
        document.hidden
      ) {

        if (animId) {
          cancelAnimationFrame(
            animId
          );

          animId = null;
        }

      } else {

        if (!animId) {
          animId =
            requestAnimationFrame(
              loop
            );
        }
      }
    }
  );

  // ============================================================
  // RESIZE
  // ============================================================

  window.addEventListener(
    'resize',
    resize,
    {
      passive: true
    }
  );

  // Initial build
  resize();

  // Start animation
  animId =
    requestAnimationFrame(
      loop
    );

  return {
    destroy,
    resize
  };
}