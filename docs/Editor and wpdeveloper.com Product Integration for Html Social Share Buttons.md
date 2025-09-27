# Technical Guideline: Integrating Html Social Share Buttons with WordPress Editors and WPDeveloper Products

---

## Introduction

As the web shifts toward performance, privacy, and minimal JavaScript, social sharing solutions for WordPress must evolve accordingly. The [Html Social Share Buttons](https://wordpress.org/plugins/html-social-share-buttons/) plugin exemplifies this direction by offering a lightweight, privacy-centric, and no-JavaScript-by-default approach to social sharing. As you rearchitect or extend this plugin, integrating it seamlessly with the most popular WordPress editors and WPDeveloper products—such as BetterDocs—ensures maximum compatibility and developer-friendly extensibility.

This guide presents an in-depth, developer-focused blueprint for integrating Html Social Share Buttons with:

- **Block-based editors (Gutenberg)**
- **Visual/page builders (Elementor, Beaver Builder, Divi, WPBakery, Thrive Architect)**
- **WPDeveloper products such as BetterDocs**
- **Headless WordPress CMS architectures (via WPGraphQL)**
- **General WordPress hooks, filters, and best practices**

For each environment, we present recommended technical strategies, hooks/filters, code snippets, and implementation details prioritizing official documentation and WordPress developer best practices.

---

## Plugin Architecture and Philosophy Overview

Before addressing specific integrations, it's critical to understand the architecture and design principles underlying **Html Social Share Buttons**:

- **Ultra-lightweight:** Less than 11KB; minimal HTTP requests; no jQuery or heavy scripting.
- **Privacy-first:** No user-tracking by default; optionally integrates privacy-compliant analytics.
- **No-JavaScript-by-default:** Markup and styles only; JavaScript only for optional enhancements.
- **Shortcode-based:** Primary output through `[zm_sh_btn ...]` shortcode.
- **Widget support:** Classic WordPress widget for Appearance > Widgets; can be extended for block/widget environments.
- **PHP implementation:** Markup fully rendered in PHP for maximal compatibility.
- **Customizable:** Through shortcode attributes, widget arguments, or PHP hooks/filters.
- **Extensible for custom icons/assets:** Developers can register their own icons or sets.
- **Security-hardened:** Input sanitization, output escaping, and consistent with WordPress coding standards (as of v2.2.1).

---

## Integration Summary Table

| Integration Target       | Entry Points        | Hook/Filter Points        | Registration Mode          | Recommended Practice                          | Compatibility Notes                  |
|-------------------------|---------------------|---------------------------|----------------------------|-----------------------------------------------|--------------------------------------|
| **Gutenberg**           | Block registration  | `register_block_type`, custom render callback | Dynamic/server block, or static block | Use block.json, dual server/client reg., server-side rendering | Full compatibility                  |
| **Elementor**           | Widget registration | Elementor Widget API      | Custom Widget / Shortcode  | Register widget and expose shortcode/attrs    | No JS conflicts, use PHP output      |
| **Beaver Builder**      | Module integration  | Add-on module system      | Custom BB Module/shortcode | Register as custom module & via shortcodes    | Confirm styling, avoid JS collision  |
| **Divi Builder**        | Module registration | Divi Module API           | Custom Module / Shortcode  | Map plugin output as Divi Module or shortcode | Confirm asset enqueuing              |
| **WPBakery**            | Shortcode mapping   | Element mapping           | Shortcode or content block | Map shortcode using Shortcode Mapper          | Supported; PHP rendering preferred   |
| **Thrive Architect**    | Element/Shortcode   | Custom Element API        | Shortcode Embed Block      | Expose via shortcode, document attributes     | Avoid extra JS; ensure style isolation|
| **BetterDocs**          | Filter/action hooks | `betterdocs_content`, filters | Shortcode, Filters, Hooks | Register shortcode or hook into render chain  | Works in DOC pages & templates       |
| **Headless WP**         | WPGraphQL           | `register_graphql_field`  | GraphQL field for shortcode| Expose sharing via GraphQL query field        | Docs recommend server-side rendering |
| **General WP**          | PHP hooks/filters   | `do_shortcode`, widgets   | Shortcode, Widget, Filter  | Adhere to plugin standards; document hooks    | Extensible, overrideable             |

*Each section below expands upon these points with detailed implementation.*

---

## Gutenberg (Block Editor) Integration

### Overview

Gutenberg (the WordPress Block Editor) is the default editing experience for WordPress. Proper integration means users can add social share buttons as a native block, configure settings visually, and enjoy seamless server-side rendering without front-end JavaScript overhead.

### Technical Approach

#### 1. **Dynamic (Server-side Rendered) Block**

- Register a custom block using `block.json`.
- Provide a server-side `render_callback` that renders the share button markup based on block attributes.
- Allow all configuration/options via block sidebar controls.

#### 2. **Static Block Using Shortcode**

- Register a block that simply renders the `[zm_sh_btn]` shortcode in the front-end output.
- Map block settings to corresponding shortcode attributes.

#### 3. **Widget Block**

- If planning for wide backward compatibility (for Widgets Screen or Appearance > Widgets), register the widget version as a block using `register_block_type_from_metadata` for use in widget areas.

### Implementation Steps

#### a) **Block Registration**

**PHP (server-side):**

```php
// In your plugin's main file or in an includable file.

add_action('init', function() {
    register_block_type(__DIR__ . '/build/social-share-block');
});
```

*Assumes you have a `block.json` in `/build/social-share-block/` directory.*

**block.json example:**
```json
{
  "$schema": "https://schemas.wp.org/trunk/block.json",
  "apiVersion": 2,
  "name": "html-social-share/buttons",
  "title": "HTML Social Share Buttons",
  "category": "widgets",
  "icon": "share",
  "description": "Add lightweight, privacy-first social share buttons.",
  "attributes": {
    "iconSet": { "type": "string", "default": "long-shadows" },
    "icons": { "type": "array", "default": ["facebook", "twitter"] },
    "url": { "type": "string", "default": "" },
    "className": { "type": "string" }
  },
  "supports": {
    "html": false
  },
  "render": "file:./render.php"
}
```

**render.php:**
```php
<?php
function html_social_share_render_block($attributes, $content) {
    ob_start();
    echo do_shortcode('[zm_sh_btn'
        . ' iconset="' . esc_attr($attributes['iconSet'] ?? 'long-shadows') . '"'
        . ' icons="' . esc_attr(implode(',', $attributes['icons'] ?? ['facebook', 'twitter'])) . '"'
        . (!empty($attributes['url']) ? ' url="' . esc_url($attributes['url']) . '"' : '')
        . (!empty($attributes['className']) ? ' class="' . esc_attr($attributes['className']) . '"' : '')
        . ']');
    return ob_get_clean();
}
```

#### b) **JavaScript (Editor UI)**

**src/edit.js**:
```js
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, CheckboxControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    // Editor-side UI code to update block attributes
    return (
        <div {...useBlockProps()}>
            {/* Preview or placeholder */}
            [Html Social Share Buttons would display here]
            {/* InspectorControls for block sidebar options */}
            <InspectorControls>
                <PanelBody title="Share Button Settings">
                    <SelectControl
                        label="Icon Set"
                        value={attributes.iconSet}
                        options={[
                            { label: 'Long Shadows', value: 'long-shadows' },
                            /* More icon sets... */
                        ]}
                        onChange={(iconSet) => setAttributes({ iconSet })}
                    />
                    <TextControl
                        label="Icons (comma separated)"
                        value={attributes.icons.join(',')}
                        onChange={(val) =>
                            setAttributes({ icons: val.split(',').map(i => i.trim()) })
                        }
                    />
                    <TextControl
                        label="Share URL"
                        value={attributes.url}
                        onChange={(url) => setAttributes({ url })}
                    />
                </PanelBody>
            </InspectorControls>
        </div>
    );
}
```
*This approach allows even the block preview to remain JS-free on the front end.*

#### c) **User Customizability**

- Surface all relevant shortcode options (icon sets, icons, url, class, etc.) as block attributes.
- Provide context-sensitive help describing privacy features, icon availability, etc.

#### d) **Compatibility and Best Practices**

- Register the block on both **server-side (PHP)** and **client-side (JS/React)** for compatibility with block hooks, FSE templates, and WPGraphQL.
- Prefer server-rendered dynamic blocks for accurate output and to ensure security.
- Use `block.json` for block metadata (makes asset registration, translation, and distribution easier).
- Avoid JS in the frontend except as an optional enhancement.

#### e) **Hooks/Filters**

- Expose PHP filters (e.g., `html_social_share_block_output`) to allow themes or other plugins to modify the output.
```php
$html = apply_filters('html_social_share_block_output', $html, $attributes);
```

### Example: Gutenberg Block Registration Flow

```php
// In your plugin init:
add_action('init', function() {
    register_block_type_from_metadata(
        __DIR__ . '/build/social-share-block',
        [ 'render_callback' => 'html_social_share_render_block' ]
    );
});
```

**References**:
- [Registering Blocks: Official Guide](https://developer.wordpress.org/block-editor/getting-started/fundamentals/registration-of-a-block/)
- [Block.json and Server-side Registration](https://wpvip.com/blog/block-json-server-side-registration/)

---

## Elementor Integration

### Overview

Elementor is one of the most popular visual page builders for WordPress. Integration can be accomplished either as a dedicated custom widget (recommended for user experience and visual control), or by exposing the plugin’s shortcode as an Elementor element.

### Technical Approach

#### 1. **Register a Custom Elementor Widget**

- Leverage Elementor’s Widget API.
- Register a custom widget (class extending `Elementor\Widget_Base`) to the Elementor widgets manager.
- Expose all configurable options as widget controls, mapped to shortcode parameters.
- Render output using PHP; avoid adding any unnecessary JS.

#### 2. **Expose Shortcode as a Widget**

- Provide a generic way for users to insert the `[zm_sh_btn]` shortcode using Elementor’s native “Shortcode” widget.
- For advanced UX, document recommended attributes/settings for best results in Elementor-built pages.

#### 3. **Asset Isolation**

- Ensure any CSS output by the plugin is scoped so as not to interfere with Elementor’s generated styles.

### Example: Registering a Custom Widget

**Plugin main file:**
```php
add_action('elementor/widgets/register', function($widgets_manager) {
    require_once __DIR__ . '/widgets/Html_Social_Share_Widget.php';
    $widgets_manager->register(new \Html_Social_Share_Widget());
});
```

**widgets/Html_Social_Share_Widget.php:**
```php
namespace HtmlSocialShare;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Html_Social_Share_Widget extends Widget_Base {
    public function get_name() { return 'html_social_share'; }
    public function get_title() { return 'HTML Social Share Buttons'; }
    public function get_icon() { return 'eicon-share'; }
    public function get_categories() { return ['general']; }

    protected function register_controls() {
        $this->start_controls_section('settings', [
            'label' => 'Settings',
        ]);
        $this->add_control('iconset', [
            'label' => 'Icon Set',
            'type' => Controls_Manager::TEXT,
        ]);
        $this->add_control('icons', [
            'label' => 'Icons (comma separated)',
            'type' => Controls_Manager::TEXT,
        ]);
        $this->add_control('url', [
            'label' => 'Share URL',
            'type' => Controls_Manager::TEXT,
        ]);
        $this->add_control('class', [
            'label' => 'Custom CSS class',
            'type' => Controls_Manager::TEXT,
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $shortcode = '[zm_sh_btn'
            . (!empty($settings['iconset']) ? ' iconset="' . esc_attr($settings['iconset']) . '"' : '')
            . (!empty($settings['icons']) ? ' icons="' . esc_attr($settings['icons']) . '"' : '')
            . (!empty($settings['url']) ? ' url="' . esc_url($settings['url']) . '"' : '')
            . (!empty($settings['class']) ? ' class="' . esc_attr($settings['class']) . '"' : '')
            . ']';
        echo do_shortcode($shortcode);
    }
}
```

*References:*
- [Elementor Widgets Developer Docs](https://developers.elementor.com/docs/widgets/)
- [Custom Elementor Widget Example](https://blog.letsgodev.com/wordpress-handbook/how-to-create-a-custom-widget-for-elementor-step-by-step/)

### Notes

- Avoid adding JS except for editor preview if absolutely necessary.
- Ensure the generated markup is HTML/CSS only for maximum page speed.
- Provide documentation on use within Elementor themes/templates.
- Test widget on both free and Pro Elementor installations.

---

## Beaver Builder Integration

### Overview

Beaver Builder allows “modules” (elements) in its drag-and-drop builder. To integrate Html Social Share Buttons, either register a custom module or provide thorough instructions for adding the plugin shortcode via Beaver Builder’s HTML or shortcode modules. Many Beaver Builder sites also use add-on packs for extra widgets—custom integration should not conflict with these.

### Technical Approaches

#### 1. **Custom Beaver Builder Module (PHP)**

- Extend the `FLBuilderModule` class to define a new module.
- Register module fields/settings to mirror your plugin’s configurable options.
- Render the output via PHP using your plugin’s PHP rendering functions.

#### 2. **Shortcode/HTML Module**

- For simpler sites, recommend using Beaver Builder’s HTML or Shortcode module with `[zm_sh_btn]` directly.

### Example: Registering a Custom Beaver Builder Module

**In your plugin:**
```php
class HTML_Social_Share_BB_Module extends FLBuilderModule {
    public function __construct() {
        parent::__construct([
            'name' => __('HTML Social Share', 'html-social-share'),
            'description' => __('Lightweight privacy-first social sharing.', 'html-social-share'),
            'category' => __('Social', 'fl-builder'),
            'group' => __('Custom', 'fl-builder')
        ]);
    }
}

FLBuilder::register_module('HTML_Social_Share_BB_Module', [
    'general' => [
        'title' => __('Settings', 'html-social-share'),
        'fields' => [
            'iconset' => [
                'type' => 'text',
                'label' => __('Icon Set', 'html-social-share')
            ],
            'icons' => [
                'type' => 'text',
                'label' => __('Icons', 'html-social-share')
            ],
            'url' => [
                'type' => 'text',
                'label' => __('Share URL', 'html-social-share')
            ],
            'class' => [
                'type' => 'text',
                'label' => __('CSS Class', 'html-social-share')
            ]
        ]
    ]
]);
```

**Module rendering template (in the module’s `frontend.php`):**
```php
<?php
echo do_shortcode('[zm_sh_btn'
    . (!empty($settings->iconset) ? ' iconset="' . esc_attr($settings->iconset) . '"' : '')
    . (!empty($settings->icons) ? ' icons="' . esc_attr($settings->icons) . '"' : '')
    . (!empty($settings->url) ? ' url="' . esc_url($settings->url) . '"' : '')
    . (!empty($settings->class) ? ' class="' . esc_attr($settings->class) . '"' : '')
    . ']');
?>
```
*This ensures output is server-rendered, accessible, and never relies on JS unless explicitly enabled.*

- [Beaver Builder Addon Integration](https://www.wpbeaverbuilder.com/integrations/)
- [Best Practices for Beaver Builder Add-ons](https://wpastra.com/plugins/best-beaver-builder-addons/)
- [Developer Module Docs](https://beaverhub.info/a-full-list-of-beaver-builder-addon-plugins/)

### Notes

- Ensure CSS isolation: avoid global styles that might conflict with Beaver Builder’s wrapper containers.
- Test module compatibility with popular Beaver Builder add-ons (Ultimate Addons, PowerPack, etc.).
- Provide widget/module icons for visual selection within the builder UI.

---

## Divi Builder Integration

### Overview

The Divi Builder (by Elegant Themes) uses a proprietary module system based in PHP and React. Integration should focus on mapping your plugin’s output to a custom Divi module, or by guiding users to use the plugin’s shortcode within Divi’s Code or Shortcode module.

### Technical Approach

#### 1. **Custom Divi Module (PHP/JSX)**

- Create a custom module, extending `ET_Builder_Module`.
- Register module fields for all configurable share button options.
- Output via server-rendered PHP for best compatibility.
- Optionally, provide live preview in the visual builder.

#### 2. **Code/Shortcode Module**

- Simplest integration: use Divi’s built-in “Code” module to insert `[zm_sh_btn]` with parameters.
- Document recommended attribute usage for the most common use cases.

### Example: Registering a Basic Divi Module

**PHP module definition:**
```php
class Html_Social_Share_Divi extends ET_Builder_Module {
    public $slug = 'html_social_share_divi';

    public function init() {
        $this->name = esc_html__('HTML Social Share', 'html-social-share');
    }

    public function get_fields() {
        return [
            'iconset' => ['label' => esc_html__('Icon Set', 'html-social-share'), 'type' => 'text'],
            'icons'   => ['label' => esc_html__('Icons', 'html-social-share'),   'type' => 'text'],
            'url'     => ['label' => esc_html__('Share URL', 'html-social-share'), 'type' => 'text'],
            'class'   => ['label' => esc_html__('CSS Class', 'html-social-share'), 'type' => 'text'],
        ];
    }

    public function render($attrs, $content = null, $render_slug) {
        $atts = shortcode_atts([
            'iconset' => '',
            'icons'   => '',
            'url'     => '',
            'class'   => '',
        ], $attrs);

        return do_shortcode('[zm_sh_btn'
            . (!empty($atts['iconset']) ? ' iconset="' . esc_attr($atts['iconset']) . '"' : '')
            . (!empty($atts['icons']) ? ' icons="' . esc_attr($atts['icons']) . '"' : '')
            . (!empty($atts['url']) ? ' url="' . esc_url($atts['url']) . '"' : '')
            . (!empty($atts['class']) ? ' class="' . esc_attr($atts['class']) . '"' : '')
            . ']');
    }
}
new Html_Social_Share_Divi();
```

*References:*
- [Divi Module Development Guide](https://talhatonmoy.com/divi-module-development-guide/)
- [Elegant Themes Divi Module Docs](https://www.elegantthemes.com/documentation/developers/divi-module/how-to-create-a-divi-builder-module/)
- [Divi Module Builder Plugin](https://diviplugins.com/divi-module-builder-plugin/)

### Notes

- Ensure all output in the `render()` method is HTML/CSS.
- Test for reactivity in Divi’s visual builder; optionally implement a custom React preview for preview area.

---

## WPBakery Page Builder Integration

### Overview

WPBakery Page Builder (formerly Visual Composer) supports mapping shortcodes to blocks/elements in the visual editor. Since Html Social Share Buttons is fully shortcode-driven, mapping is direct and robust.

### Technical Approach

#### 1. **Map Shortcode to WPBakery Element**

- Use WPBakery’s [Shortcode Mapper](https://wpbakery.com/video-tutorials/) or integrate programmatically in PHP.

**PHP registration example:**
```php
add_action('vc_before_init', function() {
    vc_map([
        'name' => __('HTML Social Share Buttons', 'html-social-share'),
        'base' => 'zm_sh_btn',
        'icon' => 'vc_icon-share',
        'category' => __('Social', 'js_composer'),
        'params' => [
            [
                'type' => 'textfield',
                'heading' => __('Icon Set', 'html-social-share'),
                'param_name' => 'iconset',
            ],
            [
                'type' => 'textfield',
                'heading' => __('Icons', 'html-social-share'),
                'param_name' => 'icons',
            ],
            [
                'type' => 'textfield',
                'heading' => __('Share URL', 'html-social-share'),
                'param_name' => 'url',
            ],
            [
                'type' => 'textfield',
                'heading' => __('CSS Class', 'html-social-share'),
                'param_name' => 'class',
            ]
        ]
    ]);
});
```

#### 2. **Native Shortcode Usage**

- Users can always insert `[zm_sh_btn ...]` through WPBakery’s Text Block or Raw HTML element.

#### 3. **Asset Conflicts**

- Confirm there’s no CSS or JS conflict due to WPBakery’s “Frontend Editor.”

*References:*
- [WPBakery Page Builder Video Tutorials](https://wpbakery.com/video-tutorials/)
- [How-To Guide: Using Page Builder Elements in Widgets and Blocks](https://www.youtube.com/watch?v=XkP35KwE7Ak)
- [WPBakery Docs](https://thejustifiable.com/wpbakery-page-builder/)

---

## Thrive Architect Integration

### Overview

Thrive Architect is another major visual/page builder for WordPress. The typical practice involves using custom elements or exposing plugin shortcodes via the “Shortcode” field.

### Technical Approach

#### 1. **Expose Shortcode for Use in Thrive Architect**

- Provide instructions for inserting `[zm_sh_btn]` shortcode in Thrive’s “Custom HTML” or “Shortcode” element.

#### 2. **Custom Element Integration**

- For advanced cases, register a custom Thrive element by extending Thrive’s developer APIs (requires knowledge of their internal module system).

- Example: Insert into any landing page via “Custom HTML” element in Thrive Builder.

*References:*
- [Thrive Architect Integration by Automator](https://automatorplugin.com/integration/thrive-architect/)
- [Instructions for integrating with Thrive Architect](https://help.stealthseminarapp.com/en/articles/2929767-how-to-integrate-with-the-thrive-architect-wordpress)
- [Plugin documentation](https://www.buddyboss.com/resources/docs/integrations/thrive-architect/)

### Notes

- Strongly recommend custom CSS scoping to guarantee visual consistency.
- Avoid adding JS—Thrive may be sensitive to extra scripting in some environments.

---

## BetterDocs (WPDeveloper.com) Integration

### Overview

BetterDocs is a widely used documentation/knowledge-base plugin by WPDeveloper.com. Integration typically focuses on adding social sharing to doc pages, articles, templates, and search/output listings. BetterDocs offers filters, template overrides, hooks, and shortcodes for extensibility.

### Technical Approach

#### 1. **Integrate via Template Hooks**

- Hook into BetterDocs Doc output using the provided `betterdocs_content` or dedicated action/filter hooks to insert sharing buttons at the end (or beginning) of each doc page.

- Example:
```php
add_filter('betterdocs_content', function($content) {
    if (is_singular('docs')) { // Only on individual doc pages
        $share = do_shortcode('[zm_sh_btn iconset="long-shadows" icons="facebook,twitter,linkedin"]');
        $content .= $share; // Append at end; can prepend if desired
    }
    return $content;
});
```

#### 2. **Custom Shortcodes**

- BetterDocs supports the use of generic shortcodes in its KB documents; users can insert `[zm_sh_btn]` directly in any doc.

#### 3. **BetterDocs Template Overrides**

- For advanced integration, override BetterDocs' single or archive templates (in your theme or child theme), inserting the social share widget where needed.

#### 4. **REST/Live Search Handling**

- For dynamic/live search results (e.g., using [SearchWP BetterDocs Integration](https://searchwp.com/documentation/extensions/betterdocs-integration/)), ensure that rendered excerpts or results include sharing links only when content type is 'docs'.

*References:*
- [BetterDocs Documentation](https://wpshortcode.org/betterdocs/)
- [BetterDocs Pro Features Overview](https://cromur.com/betterdocs-pro-wordpress-plugin/)
- [BetterDocs Search Integration](https://searchwp.com/documentation/extensions/betterdocs-integration/)

### Notes

- If using dynamic content loading (AJAX), ensure that social share markup is output on the server, not via JS.
- For privacy compliance, default buttons should not expose user identity; avoid tracking unless enabled in plugin options and reflected in the privacy policy.

---

## Headless CMS (WPGraphQL) Integration

### Overview

For decoupled/headless WordPress sites—where the frontend consumes data via GraphQL and renders with React (Next.js, Gatsby, etc.)—integration means exposing the rendered share button HTML through the GraphQL API.

### Technical Approach

#### 1. **Expose Share Button Output as a Field in WPGraphQL**

- Register a GraphQL field (e.g., `htmlSocialShare`) on the Post, Page, or Doc type.
- The field's resolver should return the rendered plugin output (HTML/CSS markup) by default.

**Example:**
```php
add_action('graphql_register_types', function() {
    register_graphql_field('Post', 'htmlSocialShare', [
        'type' => 'String',
        'description' => __('HTML Social Share Buttons output', 'html-social-share'),
        'resolve' => function($post, $args, $context, $info) {
            return do_shortcode('[zm_sh_btn url="' . get_permalink($post->ID) . '"]');
        }
    ]);
});
```

Now from GraphQL, frontend can query:
```graphql
query GetPost($id: ID!) {
  post(id: $id, idType: DATABASE_ID) {
    title
    htmlSocialShare
  }
}
```

- **Key Best Practice:** **Always enable server-side rendering.** Output is HTML-only and can be placed in any frontend React/Vue component, ensuring JS-free rendering.

*References*:
- [WPGraphQL Docs](https://www.wpgraphql.com/)
- [Custom CMS/GraphQL Guide](https://codezup.com/custom-cms-headless-wordpress-graphql/)
- [GraphQL with Next.js and WordPress](https://adamfortuna.com/wordpress-headless-cms-next-js-and-graphql/)
- [WPGraphQL + Next.js 2025 Guide](https://www.wpfixit.com/headless-wordpress-wpgraphql-nextjs-guide-2025/)

### Privacy and Compliance Notes

- Ensure that the rendered sharing button links contain only the minimal necessary information (doc/post URL, no user-identifiable data).
- Only expose share buttons in GraphQL if privacy settings allow it.

---

## WordPress Hooks & Filters Best Practices

### General Guidelines

- **Use Core Patterns:** All integration points rely on the WordPress hook/filter paradigm. Use `add_action`, `add_filter` to allow for extensibility.
- **Ensure Security**: Escape output on rendering (`esc_html`, `esc_attr`, `esc_url`). Sanitize incoming data (`sanitize_text_field`, etc.).
- **Follow Naming Conventions:** Prefix all custom hooks/filters with the plugin slug for collision avoidance (`html_social_share_`).
- **Support Extensibility:** Provide ample hooks for devs to add/override features:
    - Example: `html_social_share_render_before`, `html_social_share_render_after`, `html_social_share_button_filter` etc.

*References:*
- [WP Plugin Handbook - Hooks](https://developer.wordpress.org/plugins/hooks/)
- [Writing Extensible Plugins with Actions and Filters](https://developer.wordpress.com/docs/wordpress-com-marketplace/plugin-developer-guidelines/)

### Example Custom Filters

```php
$html = apply_filters('html_social_share_markup', $html, $attrs);
```
or
```php
do_action('html_social_share_rendered', $context);
```
Encourage devs to use or override your filters to adapt for custom use cases.

---

## Performance and Privacy Optimization

### Core Strategies

- **CSS-only and HTML-only rendering by default:** Minimize dependencies and deliver share buttons ready to render on any WordPress theme.
- **No front-end JavaScript unless opted-in by admin:** For advanced features (like tracking/analytics), use opt-in patterns.
- **Asynchronous/Deferred loading:** If JS is needed, load non-blocking and only in cases where explicitly enabled (e.g., toggle sharing counters).
- **Avoid tracking scripts:** Only add social network JS via clear, documented, admin-initiated opt-in, and honor privacy policy compliance.
- **Use properly scoped CSS:** For maximum theme and builder compatibility.

*References:*
- [Plugin Performance Optimization](https://www.strikingly.com/blog/posts/5-essential-wordpress-plugin-optimization-tips-2025)
- [Speed Optimization/Best Practices](https://wpdive.com/blog/best-wordpress-speed-optimization-plugins/)
- [WordPress Official Performance Guide](https://developer.wordpress.org/advanced-administration/performance/optimization/)

---

## Privacy-First Design and Compliance

- Never collect or transmit user-identifiable information unless clearly documented and opt-in.
- Surface and expose a privacy policy via WordPress privacy hooks (e.g., `wp_add_privacy_policy_content`).
- Offer cookie-less, script-free operation by default.
- If enabling analytics or third-party integrations, document all network requests/third-party data sharing, and provide user consent management per GDPR/CCPA guidelines.

*References:*
- [WordPress Privacy Plugin Handbook](https://developer.wordpress.org/plugins/privacy/)
- [WPBeginner Privacy Compliance Guide](https://www.wpbeginner.com/wp-tutorials/the-ultimate-guide-to-wordpress-privacy-compliance/)
- [Privacy Plugins in WordPress](https://easywebdesigntutorials.com/privacy-plugins-in-wordpress/)

---

## Implementation & Distribution Recommendations

- **Block-based distribution:** Supply blocks as part of the primary plugin package; maintain `block.json` and server-render callback for all block features.
- **Page builder integrations:** Document and bundle widget/module/element code examples for all major visual editors.
- **Hooks and filters:** Reference all available plugin hooks in developer docs/readme to encourage ecosystem growth.
- **Template parts/snippets:** For advanced users, provide template part snippets (for FSE, PHP themes, etc.) in documentation.
- **Testing:** Confirm integration on standard themes (Twenty Twenty-Four, Astra, GeneratePress), major page builders, and in both legacy and modern block environments.
- **Security hygiene:** Continue to implement WordPress code security reviews with each release (sanitize/escape, prevent XSS, etc.).

---

## Conclusion

Integrating Html Social Share Buttons across the diverse landscape of WordPress editors, builders, and niche products demands a technical approach that privileges **performance, privacy, and extensibility**. By leveraging WordPress core development paradigms—server-rendered output, hooks/filters, `block.json` registration, documented extensibility, and scriptless operation—you guarantee a future-proof integration with the ecosystem’s leading editing experiences.

All implementation examples above are designed per the latest public developer documentation for Gutenberg, Elementor, Beaver Builder, Divi, WPBakery, Thrive Architect, WPGraphQL, and BetterDocs as of September 2025. Developers following this blueprint can deliver robust, privacy-compliant, and blazingly fast social share functionality across legacy and cutting-edge WordPress stacks.

---

**For further guidance on extending or distributing Html Social Share Buttons:**
- Consult the [WordPress Plugin Developer Handbook](https://developer.wordpress.org/plugins/)
- Reference [WPDeveloper hooks and guidelines](https://developer.wordpress.com/docs/wordpress-com-marketplace/plugin-developer-guidelines/)
- Incorporate comprehensive, example-rich documentation in your plugin’s README and docs.

**This technical blueprint ensures Html Social Share Buttons will remain compatible across all major WordPress editing architectures—with the speed, privacy, and extensibility modern sites demand.**