<?php
/**
 * Placement Manager
 *
 * @package HtmlSocialShare
 */

namespace HtmlSocialShare\Services;

use HtmlSocialShare\Renderers\ButtonRenderer;
use HtmlSocialShare\Options\OptionsManager;

/**
 * Manages button placement and rendering in different locations
 */
class PlacementManager {
    /**
     * @var ButtonRenderer
     */
    private ButtonRenderer $buttonRenderer;
    
    /**
     * @var OptionsManager
     */
    private OptionsManager $optionsManager;
    
    /**
     * Constructor
     *
     * @param ButtonRenderer $buttonRenderer
     * @param OptionsManager $optionsManager
     */
    public function __construct(
        ButtonRenderer $buttonRenderer,
        OptionsManager $optionsManager
    ) {
        $this->buttonRenderer = $buttonRenderer;
        $this->optionsManager = $optionsManager;
    }
    
    /**
     * Render left placement
     *
     * @return string HTML output
     */
    public function renderLeft(): string {
        if (!$this->shouldRenderPlacement('show_left')) {
            return '';
        }
        
        $showIn = $this->optionsManager->get('show_in', []);
        $type = $showIn['show_left'] ?? '';
        
        if (empty($type)) {
            return '';
        }
        
        $options = $this->optionsManager->getAll();
        $options['iconset_type'] = $type;
        $options['class'] = 'left';
        
        return $this->buttonRenderer->render($options);
    }
    
    /**
     * Render right placement
     *
     * @return string HTML output
     */
    public function renderRight(): string {
        if (!$this->shouldRenderPlacement('show_right')) {
            return '';
        }
        
        $showIn = $this->optionsManager->get('show_in', []);
        $type = $showIn['show_right'] ?? '';
        
        if (empty($type)) {
            return '';
        }
        
        $options = $this->optionsManager->getAll();
        $options['iconset_type'] = $type;
        $options['class'] = 'right';
        
        return $this->buttonRenderer->render($options);
    }
    
    /**
     * Render before post placement
     *
     * @return string HTML output
     */
    public function renderBeforePost(): string {
        if (!$this->shouldRenderPlacement('show_before_post')) {
            return '';
        }
        
        $showIn = $this->optionsManager->get('show_in', []);
        $type = $showIn['show_before_post'] ?? '';
        
        if (empty($type)) {
            return '';
        }
        
        $options = $this->optionsManager->getAll();
        $options['iconset_type'] = $type;
        $options['class'] = 'in_shortcode';
        
        return $this->buttonRenderer->render($options);
    }
    
    /**
     * Render after post placement
     *
     * @return string HTML output
     */
    public function renderAfterPost(): string {
        if (!$this->shouldRenderPlacement('show_after_post')) {
            return '';
        }
        
        $showIn = $this->optionsManager->get('show_in', []);
        $type = $showIn['show_after_post'] ?? '';
        
        if (empty($type)) {
            return '';
        }
        
        $options = $this->optionsManager->getAll();
        $options['iconset_type'] = $type;
        $options['class'] = 'in_shortcode';
        
        return $this->buttonRenderer->render($options);
    }
    
    /**
     * Check if a placement should be rendered
     *
     * @param string $placement Placement key (show_left, show_right, etc.)
     * @return bool
     */
    public function shouldRenderPlacement(string $placement): bool {
        // Check if current post is excluded
        if ($this->optionsManager->isPostExcluded()) {
            return false;
        }
        
        // Check if placement is enabled in options
        $showIn = $this->optionsManager->get('show_in', []);
        
        return !empty($showIn[$placement]);
    }
    
    /**
     * Filter the_content to add before/after post buttons
     *
     * @param string $content Post content
     * @return string Modified content
     */
    public function filterContent(string $content): string {
        if (!is_singular() || !in_the_loop() || !is_main_query()) {
            return $content;
        }
        
        $before = $this->renderBeforePost();
        $after = $this->renderAfterPost();
        
        return $before . $content . $after;
    }
    
    /**
     * Output left and right placements in footer
     */
    public function outputFixedPlacements(): void {
        echo $this->renderLeft();
        echo $this->renderRight();
    }
}
