Base usage
==========

```php
namespace Acme\DemoBundle\Controller;

use Acme\DemoBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;

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
        $router = $this->get('router');

        // get total articles
        $total = (int) $this->
            ->getDoctrine()
            ->createQueryBuilder()
            ->select('COUNT(*)')
            ->from(Article::class, 'a')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        // build pagination
        $pagination = $this
            ->get('pagination')
            ->paginate(
                ceil($total / self::PER_PAGE), // total pages
                $request->query->get('page') // correct page
            )
            // template of link to page
            // character "%d" is replaced by the page number
            // you don't need to customize the template, because default template is "?page=%d"
            ->setPageLink('/article/?page=%d')
            // link for first page
            // as a default used the page link template
            ->setFirstPageLink('/article/')
        ;

        // get articles chunk
        $articles = $this->
            ->getDoctrine()
            ->createQueryBuilder()
            ->select('*')
            ->from(Article::class, 'a')
            ->setFirstResult(($pagination->getCurrentPage() - 1) * self::PER_PAGE)
            ->setMaxResults(self::PER_PAGE)
            ->getQuery()
            ->getResult()
        ;

        return $this->render('AcmeDemoBundle:Article:index.html.twig', [
            'total' => $total,
            'articles' => $articles,
            'pagination' => $pagination
        ]);
    }
}
```
