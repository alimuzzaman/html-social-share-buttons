### Overview

A compact, copy-pasteable set of HTML and CSS for share/link buttons that supports four positions: **before content**, **after content**, **floating left (middle, half-hidden option)**, and **floating right (middle, half-hidden option)**. Five distinct style variants are provided as CSS overrides. All variants keep icons as inline SVGs or <img> fallbacks, use CSS variables for theming, ensure 44×44px touch targets, keyboard focus visibility, reduced-motion respect, and responsive behavior.

---

### Base markup and core CSS

HTML (single share cluster; use same markup for all positions)
```html
<!-- place this where you want the cluster; adjust position classes on the wrapper -->
<div class="hss-share-cluster hss-pos-before" aria-label="Share links">
  <a class="hss-share-btn" href="/share/facebook" aria-label="Share on Facebook">
    <!-- inline SVG or <img alt=""> -->
    <svg class="hss-icon" aria-hidden="true" focusable="false">...</svg>
    <span class="hss-sr">Share on Facebook</span>
  </a>
  <a class="hss-share-btn" href="/share/twitter" aria-label="Share on Twitter">
    <svg class="hss-icon" aria-hidden="true" focusable="false">...</svg>
    <span class="hss-sr">Share on Twitter</span>
  </a>
  <a class="hss-share-btn" href="/share/link" aria-label="Copy link">
    <svg class="hss-icon" aria-hidden="true" focusable="false">...</svg>
    <span class="hss-sr">Copy link</span>
  </a>
</div>
```

Core CSS
```css
:root{
  --hss-primary: #3778C2;
  --hss-accent: #5AB89C;
  --hss-foreground: #222;
  --hss-bg: #fff;
  --hss-size: 24px;        /* icon */
  --hss-touch: 44px;       /* min target */
  --hss-gap: 8px;
  --hss-focus: 3px solid color-mix(in srgb, var(--hss-primary) 70%, black 30%);
  --hss-float-offset: 50%; /* half-hidden offset percentage */
}

/* accessibility helpers */
.hss-sr{ position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap;border:0; }

/* base cluster */
.hss-share-cluster{
  display:inline-flex;
  gap:var(--hss-gap);
  align-items:center;
  line-height:1;
}
.hss-share-btn{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  min-width:var(--hss-touch);
  min-height:var(--hss-touch);
  padding:6px;
  text-decoration:none;
  color:var(--hss-foreground);
  background:transparent;
  border:0;
  border-radius:6px;
  cursor:pointer;
}
.hss-share-btn:focus{ outline:none; box-shadow:var(--hss-focus); }
.hss-icon{ width:var(--hss-size); height:var(--hss-size); display:block; fill:currentColor; }

/* responsive scale */
@media (max-width:420px){
  :root{ --hss-size:20px; --hss-touch:40px; --hss-gap:6px; }
}

/* reduced motion */
@media (prefers-reduced-motion: reduce){
  .hss-share-btn{ transition: none !important; }
}
```

---

### Positioning utilities

CSS for the four positions (add the appropriate class to the wrapper: **hss-pos-before / hss-pos-after / hss-pos-float-left / hss-pos-float-right**)
```css
/* Inline before content (default visual: icons then label/content) */
.hss-pos-before{ display:inline-flex; gap:var(--hss-gap); align-items:center; }

/* Inline after content (use alongside content container) */
/* Example usage: place cluster with hss-pos-after after a text node */
.hss-pos-after{ display:inline-flex; gap:var(--hss-gap); align-items:center; }

/* Floating shared rules */
.hss-pos-float-left,
.hss-pos-float-right{
  position:fixed;
  top:50%;
  transform:translateY(-50%);
  display:flex;
  flex-direction:column;
  gap:var(--hss-gap);
  z-index:9999;
  transition: transform 220ms ease, opacity 180ms ease;
  background:transparent;
  padding:4px;
}

/* left float default: half-hidden peek */
.hss-pos-float-left{
  left:0;
  transform:translate(-var(--hss-float-offset), -50%);
}
.hss-pos-float-left.hss-show{ transform:translate(0, -50%); }

/* right float default: half-hidden peek */
.hss-pos-float-right{
  right:0;
  transform:translate(var(--hss-float-offset), -50%);
}
.hss-pos-float-right.hss-show{ transform:translate(0, -50%); }

/* reveal on keyboard focus (no JS required) */
.hss-pos-float-left:focus-within,
.hss-pos-float-right:focus-within{ transform:translate(0, -50%); }

/* mobile behavior: hide floats or collapse into a compact FAB */
@media (max-width:640px){
  .hss-pos-float-left, .hss-pos-float-right{ display:none; }
  /* alternative: show as bottom fixed bar by adding .hss-mobile-bar */
  .hss-mobile-bar{ position:fixed; left:0; right:0; bottom:0; display:flex; justify-content:center; gap:12px; padding:8px; background:rgba(255,255,255,0.96); z-index:9999; }
}
```

---

### Five style variants (copy the base then add one of the following overrides)

#### Minimal Flat Style
```css
/* Minimal Flat: monochrome icons, subtle brand hover fill */
.hss-style-minimal .hss-share-btn{ color:var(--hss-foreground); background:transparent; }
.hss-style-minimal .hss-share-btn:hover{ color:var(--hss-primary); background:transparent; transform:translateX(0); }
.hss-style-minimal .hss-share-btn:active{ opacity:0.95; }
```
Notes: monochrome by default, subtle primary color on hover.

#### Rounded Accent Style
```css
/* Rounded Accent: circular buttons with accent border */
.hss-style-rounded .hss-share-btn{
  border-radius:999px;
  background:transparent;
  border:1px solid color-mix(in srgb, var(--hss-accent) 18%, transparent);
  padding:8px;
}
.hss-style-rounded .hss-share-btn:hover{ background:color-mix(in srgb, var(--hss-accent) 14%, transparent); color:var(--hss-accent); }
.hss-style-rounded .hss-icon{ width:20px; height:20px; }
```
Notes: improved touch targets, half-hidden floats show circular peek edge.

#### Outlined Subtle Style
```css
/* Outlined Subtle: thin strokes, text-like integration */
.hss-style-outlined .hss-share-btn{ color:#323A45; background:transparent; }
.hss-style-outlined .hss-icon{ stroke:currentColor; fill:none; stroke-width:1; }
.hss-style-outlined .hss-share-btn:hover{ color:var(--hss-primary); background:transparent; transform:translateX(0); }
.hss-style-outlined .hss-share-btn:focus{ box-shadow: var(--hss-focus); }
```
Notes: lightweight typographic feel; ensure SVGs support stroke-only rendering.

#### Gradient Hover Style
```css
/* Gradient Hover: flat base with gradient on hover (progressive enhancement) */
.hss-style-gradient .hss-share-btn{ color:var(--hss-foreground); background:transparent; transition: background 220ms ease, transform 180ms ease; }
.hss-style-gradient .hss-share-btn:hover{
  color:#fff;
  background:linear-gradient(135deg, var(--hss-primary), var(--hss-accent));
  transform:translateX(0);
}
.hss-style-gradient .hss-share-btn .hss-icon{ filter: none; }
@media (prefers-reduced-motion: reduce){ .hss-style-gradient .hss-share-btn{ transition: none; } }
```
Notes: gradient only used on interaction; core rendering remains flat.

#### High-Contrast Accessible Style
```css
/* High-Contrast Accessible: bold fills and larger spacing */
.hss-style-contrast .hss-share-btn{ background:var(--hss-foreground); color:#fff; border-radius:8px; padding:8px; gap:10px; }
.hss-style-contrast .hss-icon{ width:28px; height:28px; }
.hss-style-contrast .hss-share-btn:hover{ filter:brightness(1.05); }
.hss-style-contrast .hss-share-btn:focus{ outline: 4px solid color-mix(in srgb, var(--hss-accent) 60%, black 10%); outline-offset:2px; }
```
Notes: targets WCAG AAA contrast; ideal for inclusive variants.

---

### Usage, accessibility, and small implementation notes

- Add a style class on the cluster wrapper: e.g., <div class="hss-share-cluster hss-pos-float-left hss-style-rounded">.
- Ensure inline SVGs include aria-hidden="true" and the anchor/button contains a descriptive aria-label or visible text.
- For half-hidden floats, use the default transform offsets; add the class **hss-show** (JS or CSS :focus-within will add reveal) to fully show.
- Respect prefers-reduced-motion and prefers-color-scheme with overrides if needed.
- For mobile, either hide floating widgets or switch to .hss-mobile-bar for a bottom-centered compact bar.

---