<?php


namespace HansSchouten\LaravelPageBuilder;


use PHPageBuilder\Modules\GrapesJS\Block\BlockAdapter;

class BlockAdapterWrapper extends BlockAdapter
{
    /**
     * Return an array representation of the theme block, for adding as a block to GrapesJS.
     *
     * @return array
     * @throws \Exception
     */
    public function getBlockManagerArray()
    {
        $content = $this->pageRenderer->renderBlock($this->block->getSlug());

        $img = '';
        if ($this->block->getThumbPath()) {
            $img = '<div class="block-thumb"><img src="' . $this->block->getThumbUrl() . '"></div>';
        }

        $data = [
            'label' => $img . $this->getTitle(),
            'category' => $this->getCategory(),
            'content' => $content
        ];

        if (! $img) {
            $iconClass = 'fa fa-bars';
            if ($this->block->get('icon')) {
                $iconClass = $this->block->get('icon');
            }
            $data['attributes'] = ['class' => $iconClass];
        }

        return $data;
    }
}