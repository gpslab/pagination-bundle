From HTTP request
=================

```php
namespace Acme\DemoBundle\Controller;

use Acme\DemoBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;
use Acme\DemoBundle\Entity\Article;

class ArticleController extends Controller
{
    /**
     * Articles per page.
     */
    private const PER_PAGE = 100;

    /**
     * @Configuration\Route("/article/", name="article_index")
     * @Configuration\Method({"GET"})
     */
    public function index(Request $request): Response
    {
        $rep = $this->getDoctrine()->getRepository(Article::calss);

        $total = $rep->getTotalPublished();
        $total_pages = ceil($total / self::PER_PAGE);

        // build pagination
        $pagination = $this->get('pagination')->paginateRequest(
            $request,
            $total_pages,
            'p', // request parameter for page number
            UrlGeneratorInterface::ABSOLUTE_URL // build absolute url in pagination
        );

        // get articles chunk
        $articles = $rep->getPublished(
            self::PER_PAGE, // limit
            ($pagination->getCurrentPage() - 1) * self::PER_PAGE // offset
        );

        return $this->render('AcmeDemoBundle:Article:index.html.twig', [
            'total' => $total,
            'articles' => $articles,
            'pagination' => $pagination
        ]);
    }
}
```
