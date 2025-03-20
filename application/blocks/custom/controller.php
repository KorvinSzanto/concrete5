<?php

namespace Application\Block\Custom;

use Concrete\Core\Block\BlockController;


class Controller extends BlockController
{
    /**
     * @var string
     */
    public $content;

    /**
     * @var string
     */
    protected $btTable = 'btCustom';

    /**
     * @var int
     */
    protected $btInterfaceWidth = 600;

    /**
     * @var int
     */
    protected $btInterfaceHeight = 465;

    /**
     * @var bool
     */
    protected $btCacheBlockRecord = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutput = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputOnPost = true;

    /**
     * @var bool
     */
    protected $btSupportsInlineEdit = true;

    /**
     * @var bool
     */
    protected $btSupportsInlineAdd = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputForRegisteredUsers = false;

    /**
     * @var int
     */
    protected $btCacheBlockOutputLifetime = 0; //until manually updated or cleared

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Custom block.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Custom');
    }

}
