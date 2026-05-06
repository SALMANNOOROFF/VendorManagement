---
name: High-End Enterprise VMS
colors:
  surface: '#f7fafc'
  surface-dim: '#d7dadc'
  surface-bright: '#f7fafc'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f1f4f6'
  surface-container: '#ebeef0'
  surface-container-high: '#e5e9eb'
  surface-container-highest: '#e0e3e5'
  on-surface: '#181c1e'
  on-surface-variant: '#444748'
  inverse-surface: '#2d3133'
  inverse-on-surface: '#eef1f3'
  outline: '#747878'
  outline-variant: '#c4c7c8'
  surface-tint: '#5d5f5f'
  primary: '#5d5f5f'
  on-primary: '#ffffff'
  primary-container: '#ffffff'
  on-primary-container: '#747676'
  inverse-primary: '#c6c6c7'
  secondary: '#4e6078'
  on-secondary: '#ffffff'
  secondary-container: '#cfe1fd'
  on-secondary-container: '#52647c'
  tertiary: '#5d5f5f'
  on-tertiary: '#ffffff'
  tertiary-container: '#ffffff'
  on-tertiary-container: '#747676'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#e2e2e2'
  primary-fixed-dim: '#c6c6c7'
  on-primary-fixed: '#1a1c1c'
  on-primary-fixed-variant: '#454747'
  secondary-fixed: '#d3e4ff'
  secondary-fixed-dim: '#b6c8e4'
  on-secondary-fixed: '#091c31'
  on-secondary-fixed-variant: '#37485f'
  tertiary-fixed: '#e2e2e2'
  tertiary-fixed-dim: '#c6c6c7'
  on-tertiary-fixed: '#1a1c1c'
  on-tertiary-fixed-variant: '#454747'
  background: '#f7fafc'
  on-background: '#181c1e'
  surface-variant: '#e0e3e5'
typography:
  h1:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '700'
    lineHeight: '1.2'
    letterSpacing: -0.02em
  h2:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: '1.3'
    letterSpacing: -0.01em
  h3:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '600'
    lineHeight: '1.4'
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: '1.5'
  label-caps:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: '1'
    letterSpacing: 0.05em
  status:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '500'
    lineHeight: '1'
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  unit: 8px
  container-max: 1440px
  sidebar-width: 260px
  gutter: 24px
  margin-page: 40px
  card-padding: 24px
---

## Brand & Style

The brand personality is authoritative yet technologically forward-thinking. It targets enterprise procurement officers and supply chain directors who require a tool that feels as reliable as a legacy system but as fast as a modern SaaS startup. The UI should evoke a sense of clarity, efficiency, and premium "Swiss" precision.

The design system utilizes a **Corporate Modern** style with a heavy emphasis on **Minimalism**. By using a restricted palette of deep navy and crisp white, the interface recedes to let the vendor data and metrics take center stage. The aesthetic is strictly flat, avoiding skeuomorphic gradients or heavy bevels, instead relying on precise geometry and generous whitespace to communicate sophistication.

## Colors

The palette is anchored by **Clean Aesthetic White (#FFFFFF)** for the main workspace and content cards, ensuring maximum legibility and a sense of "digital air." This is contrasted sharply by **Deep Professional Blue (#0B1E33)**, which is reserved for the fixed sidebar and primary typography to establish a clear structural hierarchy.

**Vibrant Cyan (#00E5FF)** serves as the high-energy accent. It is used sparingly but purposefully for interactive elements, progress indicators, and active states. This neon-adjacent hue injects a modern, tech-centric feel into the otherwise conservative corporate palette. Neutral grays are used exclusively for subtle borders and secondary backgrounds to prevent the interface from feeling stark.

## Typography

This design system utilizes **Inter** exclusively to achieve a systematic, utilitarian aesthetic. As a typeface designed for screens, its tall x-height and neutral character provide the clarity needed for complex data grids and vendor profiles.

Headlines use a tighter letter-spacing and heavier weights in the Deep Professional Blue to command attention. Body text is optimized for long-form reading in procurement contracts, utilizing a slightly softer gray to reduce eye strain. Data labels use an uppercase style with increased tracking to differentiate them from interactive text.

## Layout & Spacing

The layout follows a **Fixed-Fluid Hybrid** model. A fixed sidebar remains anchored to the left, while the main content area utilizes a fluid 12-column grid that expands to a maximum width of 1440px to maintain readability on ultra-wide monitors.

The spacing rhythm is built on a strict **8px base unit**. Generous whitespace (margins of 40px) is used to separate high-level sections, creating a "premium gallery" feel where information is never cramped. Padding within cards and tables is consistently 24px (3 units) to ensure data-heavy views remain approachable and organized.

## Elevation & Depth

In keeping with the flat design philosophy, depth is communicated through **tonal layering and low-contrast outlines** rather than heavy shadows. 

The primary canvas is a very light neutral, while cards and content areas are pure white. To create separation, cards utilize a 1px border in a soft neutral or a very subtle "ambient shadow"—a 4px blur with 4% opacity of the secondary blue. The navbar uses a slightly more pronounced but still restrained shadow to indicate it sits above the scrolling content. This creates a "stacked paper" effect that feels light and modern.

## Shapes

The design system employs a **Rounded (8px)** shape language. This specific radius strikes a balance between the clinical sharpness of 0px corners and the overly casual nature of fully rounded pill shapes. 

This 8px radius is applied consistently across cards, action buttons, and input fields. Smaller elements, such as status badges and checkboxes, may use a 4px radius to maintain visual proportion. The consistency of these radiuses reinforces the professional, systematic nature of the enterprise software.

## Components

**Buttons:** Primary actions are solid Cyan (#00E5FF) with White text, using the 8px radius. Secondary actions use a ghost style with a Deep Blue border and text.

**Fixed Sidebar:** Rendered in Deep Blue (#0B1E33). Icons should be stroke-based and 20px, turning Cyan when active. An active menu item is indicated by a vertical Cyan bar on the left edge.

**Inputs:** Ghost-style inputs with a 1px light gray border. Upon focus, the border transitions to 1px Cyan (#00E5FF) with a subtle 2px Cyan outer glow.

**Status Badges:** 'Active' status badges feature a light Cyan background (15% opacity) with solid Cyan text. Other statuses (e.g., Pending, Inactive) use the same logic with neutral or semantic colors.

**Cards:** Pure White (#FFFFFF) backgrounds with 8px rounded corners and a 1px light gray border. Headers within cards should be separated by a subtle horizontal rule.

**Data Tables:** Clean, no vertical borders. Row hover states should use a very faint Blue tint to guide the user's eye without breaking the minimalist aesthetic.