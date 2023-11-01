<?php
/**
 * Created by PhpStorm.
 * User: mercury
 * Date: 31.12.18
 * Time: 13:48
 */

namespace MercuryKojo\Bundle\UniversalCommentBundle\Controller;

use MercuryKojo\Bundle\UniversalCommentBundle\Event\ValidateCommentEvent;
use Pimcore\Controller\FrontendController;
use Pimcore\Bundle\ApplicationLoggerBundle\ApplicationLogger;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Service;
use Pimcore\Model\DataObject\UniversalComment;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UniversalCommentController
 * @package MercuryKojo\Bundle\UniversalCommentBundle\Controller
 * @Route("/{_locale}/ucb")
 */
class UniversalCommentController extends FrontendController {

    /**
     * @var ApplicationLogger
     */
    private $logger;

    public function __construct(ApplicationLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/comment/add", name="ucb_comment_add")
     * @param Request $request
     * @throws \Exception
     */
    public function addComment(Request $request, EventDispatcherInterface $dispatcher, UserInterface $user = null)
    {
        $content = $request->get('editordata');
        $id = $request->get('id');

        $validateEvent = $dispatcher->dispatch(new ValidateCommentEvent('validateComment', [
            'id' => $id,
            'content' => $content
        ]), ValidateCommentEvent::EVENT_NAME);

        if ($validateEvent->isCommentValid()) {
            $comment = new UniversalComment();
            $comment->setPublished(true);
            $comment->setParent(Service::createFolderByPath('Comments/' . $id . '/'));
            $comment->setKey(md5(time()));
            $comment->setIp($request->getClientIp());
            if ($user instanceof AbstractObject) {
                $comment->setPoster($user);
            }
            if (intval($request->get('commentId'))) {
                $parentComment = UniversalComment::getById(intval($request->get('commentId')));
                if ($parentComment instanceof UniversalComment) {
                    $comment->setParentComment($parentComment);
                }
            }
            $comment->setContent($content);
            $comment->setSourceId($id);

            $comment->save();
        } else {
            $this->logger->warning('No Content Comment. Data: ' . json_encode($request->request->all()), ['component' => 'UniversalCommentBundle']);
        }
        return $this->redirect($request->get('_target_path') . '#' . $id);
    }

    /**
     * @Route("/comment/remove/{id}", name="ucb_comment_remove")
     * @param Request $request
     * @param UserInterface $user
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeComment(Request $request, UserInterface $user, $id)
    {
        if (intval($id)) {
            $comment = UniversalComment::getById(intval($id));
            if ($comment instanceof UniversalComment) {
                if (in_array('ROLE_UCB_ADMIN', $user->getRoles()) || ($comment->getPoster() && $comment->getPoster()->getId() == $user->getId())) {
                    $comment->delete();
                } else {
                    $this->logger->warning('User not allowes to delete Comment. Data: ' . json_encode($request->request->all()), ['component' => 'UniversalCommentBundle']);
                }
            } else {
                $this->logger->warning('Comment not found. Data: ' . json_encode($request->request->all()), ['component' => 'UniversalCommentBundle']);
            }
        } else {
            $this->logger->warning('No Comment Id given. Data: ' . json_encode($request->request->all()), ['component' => 'UniversalCommentBundle']);
        }
        return $this->redirect($request->get('_target_path'));
    }
}
