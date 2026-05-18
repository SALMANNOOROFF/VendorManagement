---
name: Apex Insight
colors:
  surface: '#f8f9ff'
  surface-dim: '#cbdbf5'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff4ff'
  surface-container: '#e5eeff'
  surface-container-high: '#dce9ff'
  surface-container-highest: '#d3e4fe'
  on-surface: '#0b1c30'
  on-surface-variant: '#464554'
  inverse-surface: '#213145'
  inverse-on-surface: '#eaf1ff'
  outline: '#767586'
  outline-variant: '#c7c4d7'
  surface-tint: '#494bd6'
  primary: '#4648d4'
  on-primary: '#ffffff'
  primary-container: '#6063ee'
  on-primary-container: '#fffbff'
  inverse-primary: '#c0c1ff'
  secondary: '#585e6c'
  on-secondary: '#ffffff'
  secondary-container: '#dde2f3'
  on-secondary-container: '#5e6473'
  tertiary: '#006c49'
  on-tertiary: '#ffffff'
  tertiary-container: '#00885d'
  on-tertiary-container: '#000703'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#e1e0ff'
  primary-fixed-dim: '#c0c1ff'
  on-primary-fixed: '#07006c'
  on-primary-fixed-variant: '#2f2ebe'
  secondary-fixed: '#dde2f3'
  secondary-fixed-dim: '#c1c6d7'
  on-secondary-fixed: '#161c27'
  on-secondary-fixed-variant: '#414754'
  tertiary-fixed: '#6ffbbe'
  tertiary-fixed-dim: '#4edea3'
  on-tertiary-fixed: '#002113'
  on-tertiary-fixed-variant: '#005236'
  background: '#f8f9ff'
  on-background: '#0b1c30'
  surface-variant: '#d3e4fe'
typography:
  headline-lg:
    fontFamily: Inter
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 36px
    letterSpacing: -0.02em
  headline-md:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
    letterSpacing: -0.01em
  headline-sm:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 24px
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  body-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 18px
  label-bold:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '700'
    lineHeight: 16px
    letterSpacing: 0.05em
  data-display:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
    letterSpacing: -0.03em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  container-margin: 24px
  gutter: 20px
  card-padding: 24px
  stack-sm: 8px
  stack-md: 16px
---

## Brand & Style

This design system is engineered for high-density information environments where clarity and efficiency are paramount. The aesthetic follows a **Corporate / Modern** approach, blending the structured reliability of enterprise software with the vibrant, approachable feel of contemporary SaaS.

The personality is authoritative yet helpful—providing the user with a powerful "control center" feel. It prioritizes functional minimalism, using a light-blue tinted background to reduce eye strain during long working sessions, while employing sharp accents of purple and emerald to guide the user's attention toward critical actions and data insights.

## Colors

The palette is anchored by a deep navy/charcoal (#1a202c) used for high-level navigation and headers to establish a strong structural frame. The primary action color is a vibrant Indigo (#6366f1), providing a modern, digital-first feel for buttons and active states.

- **Success & Positive:** Use Emerald (#10b981) for "Approved" statuses and growth metrics.
- **Warning & Pending:** Use Amber (#f59e0b) for cautionary data or tasks awaiting action.
- **Surface & Background:** The main canvas is a soft Slate (#f8fafc), which allows white card components to pop with subtle definition.
- **Data Viz:** A diverse, high-contrast 5-color palette ensures distinct categories remain legible in complex charts and donut distributions.

## Typography

This design system utilizes **Inter** exclusively to leverage its exceptional legibility in data-heavy interfaces. 

- **Hierarchy:** Use `data-display` for hero metrics within stats cards. 
- **Labels:** `label-bold` is intended for table headers and small metadata categories, using uppercase styling to differentiate from interactive body text.
- **Mobile Scaling:** On mobile devices, `headline-lg` should scale down to 24px. Large data points should maintain their weight but may reduce to 28px to prevent container overflow.

## Layout & Spacing

The system uses a **Fluid Grid** model with fixed maximum widths for readability on ultra-wide monitors.

- **Grid:** A 12-column system is used for the main dashboard. Standard metric cards occupy 3 columns (4 per row), while primary data visualizations occupy 6-8 columns.
- **Breakpoints:**
  - **Desktop (1280px+):** Full 12-column layout, 24px margins.
  - **Tablet (768px - 1279px):** 6-column layout, cards stack into 2 columns.
  - **Mobile (Below 768px):** Single column layout, 16px horizontal margins.
- **Rhythm:** An 8px base unit governs all padding and margins to maintain a strict geometric alignment.

## Elevation & Depth

Visual hierarchy is achieved through a **Tonal Layering** approach combined with **Ambient Shadows**.

- **Level 0 (Background):** The Slate #f8fafc background acts as the lowest layer.
- **Level 1 (Cards):** Main content containers are pure white with a very soft, diffused shadow (`box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05)`).
- **Level 2 (Dropdowns/Modals):** Elements that float above the grid use a more pronounced shadow to indicate temporal presence.
- **Outlines:** Use 1px borders in #e2e8f0 for internal card dividers and table rows to provide structure without adding visual weight.

## Shapes

The design system uses a **Rounded** (Level 2) shape language to soften the density of the data-heavy layout.

- **Standard (8px):** Applied to cards, input fields, and primary buttons.
- **Large (16px):** Used for decorative elements or containers that hold groups of cards.
- **Pill:** Reserved exclusively for status badges and tags (e.g., "Approved", "Pending") to make them instantly recognizable as non-interactive status indicators.

## Components

### Buttons
- **Primary:** Solid Indigo (#6366f1) with white text. 8px corner radius.
- **Secondary:** Transparent background with Indigo border and text.
- **Ghost:** No border, Slate text, becomes lightly gray on hover.

### Cards & Statistics
- Statistics cards should feature a large `data-display` value, a descriptive `headline-sm` title, and a small icon or sparkline colored by the metric's health (e.g., green for positive growth).

### Tables
- Headers use `label-bold` with a light gray background (#f1f5f9).
- Rows feature a subtle hover state (#f8fafc) and 1px bottom borders.
- Text alignment: Numerical data should be right-aligned; status badges and text should be left-aligned.

### Status Badges
- Use a "Soft Fill" style: A 10-15% opacity version of the status color for the background with the 100% opacity color for the text. 
- *Example:* "Approved" uses a light emerald background with dark emerald text.

### Inputs
- Fields use a 1px border (#cbd5e1), white background, and 8px radius. On focus, the border transitions to Indigo with a subtle outer glow.