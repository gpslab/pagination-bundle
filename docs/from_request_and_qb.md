From HTTP request and QueryBuilder
==================================

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
        // create get articles query
        // would be better move this query to repository class
        $query = $this
            ->getDoctrine()
            ->getRepository(Article::calss)
            ->createQueryBuilder('a')
            ->where('a.enabled = :enabled')
            ->setParameter('enabled', true)
        ;

        // build pagination
        $pagination = $this->get('pagination')->paginateRequestQuery(
            $request,
            $query,
            self::PER_PAGE, // articles per page
            'p', // request parameter for page number
            UrlGeneratorInterface::ABSOLUTE_URL // build absolute url in pagination
        );

        return $this->render('AcmeDemoBundle:Article:index.html.twig', [
            'total' => $pagination->getTotalPages(), // total pages
            'articles' => $query->getQuery()->getResult(), // get articles chunk
            'pagination' => $pagination
        ]);
    }
}
```
