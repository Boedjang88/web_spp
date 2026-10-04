# DESIGN.md - Impeccable Design System Guidelines

## Visual Principles (Anti-Slop Standard)
1. **Micro-Typography Focus**: Use `Plus Jakarta Sans` for clean UI sans-serif hierarchy and `JetBrains Mono` for tabular numbers, NIMs, NIDNs, and financial ledgers.
2. **Tinted Grays & Brand Color System**: No pure #000000 or #ffffff grays. All neutrals are tinted with Slate/Indigo (`#0f172a`, `#1e293b`, `#f8fafc`).
3. **No Card Nesting Tunnel**: Avoid nesting cards inside cards inside cards. Use clean dividers (`border-slate-200/80` or `dark:border-slate-800`), whitespace rhythm, and clear typography scale.
4. **Purposeful Contrast & Accessibility**: High contrast status indicators (`emerald` for Success/Hadir, `rose` for Alpa/Error, `amber` for Sakit/Warning, `indigo` for Primary Actions).
5. **Contextual Iconography**: No decorative icon tiles above every heading. Icons are functional, mono-stroke SVGs.

## Color Palette & Tokens
- **Primary Accent**: Slate Indigo (`#4f46e5`, `#4338ca`)
- **Background Light**: Neutral Slate 50 (`#f8fafc`)
- **Background Dark**: Deep Slate 950 (`#070b14`)
- **Surface Card Light**: `#ffffff` with 1px border (`#e2e8f0`)
- **Surface Card Dark**: `#0f172a` with 1px border (`#1e293b`)
- **Status Badges**:
  - Success / Hadir / Approved: `bg-emerald-50 text-emerald-700 border-emerald-200`
  - Warning / Sakit / Pending: `bg-amber-50 text-amber-700 border-amber-200`
  - Danger / Alpa / Rejected: `bg-rose-50 text-rose-700 border-rose-200`

## Micro-Interactions & Animation Rules
- Transitions must be fast and intentional (`transition duration-150 ease-out`).
- Avoid bounce/elastic easings (feels outdated/gimmicky).
- Active states use subtle scaling (`active:scale-95`).
