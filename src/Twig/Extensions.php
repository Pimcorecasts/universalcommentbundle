<?php
namespace MercuryKojo\Bundle\UniversalCommentBundle\Twig;

use MercuryKojo\Bundle\UniversalCommentBundle\Templating\Helper\UniversalCommentHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Extensions extends AbstractExtension
{

    /**
     * @var UniversalCommentHelper
     */
    private $commentHelper;

    /**
     * Extensions constructor.
     * @param UniversalCommentHelper $commentHelper
     */
    public function __construct(UniversalCommentHelper $commentHelper)
    {
        $this->commentHelper = $commentHelper;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('universalComment', [$this, 'universalComment'], [ 'is_safe' => ['html'] ]),
        ];
    }

    /**
     * @param string|null $id
     * @param array $config
     * @return string|UniversalCommentHelper
     * @throws \Exception
     */
    public function universalComment( string $id = null, array $config = [] ){
        return $this->commentHelper->__invoke($id, $config);
    }

}
