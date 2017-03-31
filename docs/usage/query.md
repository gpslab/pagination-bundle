From QueryBuilder
=================

```php
namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;
use Acme\DemoBundle\Entity\Article;

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
        // create get articles query
        // would be better move this query to repository class
        $query = $this
            ->getDoctrine()
            ->getRepository('AcmeDemoBundle:Article')
            ->createQueryBuilder('a')
            ->where('a.enabled = :enabled')
            ->setParameter('enabled', true)
        ;

        // build pagination
        $pagination = $this
            ->get('pagination')
            ->paginateQuery(
                $query,
                self::PER_PAGE,
                $request->query->get('page') // correct page
            )
            // build page link
            ->setPageLink(function($page) {
                return $this->generateUrl('article_index', ['page' => $page]);
            })
            // build link for first page
            ->setFirstPageLink($this->generateUrl('article_index'))
        ;

        return $this->render('AcmeDemoBundle:Article:index.html.twig', [
            'total' => $pagination->getTotalPages(), // total pages
            'articles' => $query->getQuery()->getResult(), // get articles chunk
            'pagination' => $pagination
        ]);
    }
}
```
