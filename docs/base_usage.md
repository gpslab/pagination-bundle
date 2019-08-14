Base usage
==========

```php
namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;

class ArticleController extends Controller
{
    /**
     * Articles per page.
     *
     * @var int
     */
    const PER_PAGE = 100;

    /**
     * @Configuration\Route("/article/", name="article_index")
     * @Configuration\Method({"GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        $router = $this->get('router');

        // get total articles
        $total = (int)$em
            ->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('AcmeDemoBundle:Article', 'a')
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
        $articles = $em
            ->createQueryBuilder()
            ->select('*')
            ->from('AcmeDemoBundle:Article', 'a')
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
