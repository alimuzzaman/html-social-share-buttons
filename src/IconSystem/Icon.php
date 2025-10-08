<?php
/**
 * Icon Data Structure
 *
 * @package HtmlSocialShare
 */

namespace HtmlSocialShare\IconSystem;

/**
 * Represents a single social network icon
 */
class Icon {
    /**
     * @var string Unique identifier (e.g., 'facebook')
     */
    public string $id;
    
    /**
     * @var string Human-readable name (e.g., 'Facebook')
     */
    public string $name;
    
    /**
     * @var string CSS class name
     */
    public string $class;
    
    /**
     * @var string Image filename
     */
    public string $image;
    
    /**
     * @var string URL template with placeholders
     */
    public string $urlTemplate;
    
    /**
     * Constructor
     *
     * @param array $data Icon data
     */
    public function __construct(array $data) {
        $this->id = $data['id'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->class = $data['class'] ?? $this->id;
        $this->image = $data['image'] ?? $this->id . '.png';
        $this->urlTemplate = $data['url'] ?? '';
    }
    
    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'class' => $this->class,
            'image' => $this->image,
            'url' => $this->urlTemplate,
        ];
    }
}
