<?php
namespace GridTest;

use Grid\Grid;
use Grid\Interfaces\TranslateInterface;
use Grid\Renderer\HtmlRenderer;
use Grid\Util\Hydrator;
use Grid\Source\ArraySource;
use Grid\Factory\StaticFactory;
use Grid\Interfaces\InputsInterface;
use Grid\Interfaces\LinksInterface;

use PHPUnit\Framework\TestCase;

use \Exception;

class GridTest extends TestCase implements TranslateInterface
{
    protected $data = [
        [1, 2],
        [3, 4],
        [5, 6]
    ];
    
    public function testGridEmptyConstruct()
    {
        $instance = new Grid;
        $this->assertTrue(true);
    }

    public function testIterator()
    {
        $instance = new Grid;
        $instance[] = $this;
        $this->assertTrue(isset($instance[self::class]));
        
        $instance->offsetUnset(self::class);
        $this->assertFalse(isset($instance[self::class]));
        $instance->offsetSet('test', $this);
        $this->assertTrue(isset($instance[self::class]));

        try {
            $instance['test'] = new \DateTime;
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $instance[] = 1;
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testGridId()
    {
        $id = 'my-id';
        $instance = new Grid(['id' => $id]);
        $this->assertTrue(is_string($instance->getId()));
        $this->assertTrue($instance->getId() === $id);
    }

    public function testColumns()
    {
        $instance = new Grid;
        $instance[] = new \Grid\Column\Column(['name' => 'test', 'dataType' => \Grid\DataType\Str::class]);
        $this->assertTrue(count($instance->getColumns()) === 1);
        $this->assertTrue(isset($instance[\Grid\Plugin\DataTypesPlugin::class]));

        $this->assertTrue($instance->getColumns()['test'] instanceof \Grid\Column\AbstractColumn);

//        $this->expectException(\Exception::class);
        $this->assertFalse(isset($instance->getColumns()['test2']));
    }

    public function testTranslate()
    {
        $instance = new Grid;
        $this->assertTrue($instance->translate('test') === 'test');

        $instance = new Grid;
        $instance[] = $this;
        $this->assertTrue($instance->translate('test') === '1test');
    }

    public function translate(string $string) : string
    {
        return '1' . $string;
    }

    public function testRender()
    {
        $instance = new Grid;
        $instance[] = new HtmlRenderer;
        $instance[] = new Hydrator;
        $instance[] = new ArraySource(['driver' => $this->data]);
        $this->assertTrue(is_string($instance->render()));
        $this->assertTrue(count($this->data) === $instance->getCount());
    }

    public function testFactory()
    {
        $config = [
            \Grid\Renderer\HtmlRenderer::class,
            \Grid\Plugin\PaginationPlugin::class,
            \Grid\Util\Hydrator::class,
            [
                'class' => \Grid\Plugin\HeaderPlugin::class,
                'options' => [
                    'position' => 'ASD',
                ]
            ],
            [
                'class' => \Grid\Column\Column::class,
                'options' => [
                    'name' => 'id',
                    'label' => '#',
                    'dbFields' => 'id',
                    'sortable' => true,
                    'selectable' => true,
                    'searchable' => true,
                ]
            ],
            [
                'class' => \Grid\Column\Column::class,
                'options' => [
                    'name' => 'name',
                    'label' => 'Name',
                    'dbFields' => 'name',
                ]
            ],
            [
                'class' => \Grid\Source\ArraySource::class,
                'options' => [
                    'driver' => [
                        ['id' => 1, 'name' => 'name 1'],
                        ['id' => 2, 'name' => 'name 2']
                    ],
                ]
            ],
            [
                'class' => \Grid\Plugin\ColumnFilterablePlugin::class,
                'options' => [
                    'markMatches' => true,
                ]
            ],
            [
                'class' => \Grid\Util\Links::class,
                'options' => [
                    'params' => [
                        'grid' => [
                            'grid-id' => [
                                'searchable' => [
                                    'id' => 1
                                ],
                                'selectable' => [
                                    'id' => 1
                                ]
                            ]
                        ]
                    ],
                ]
            ],
            \Grid\Plugin\ColumnSortablePlugin::class,
        ];

        //grid[grid-id][searchable][title]=cable

        $_SERVER['REQUEST_URI']= '/';

        $grid = StaticFactory::factory($config);

        $this->assertTrue(count($grid->getColumns()) === 2);
        $this->assertTrue(count($grid[\Grid\Source\ArraySource::class]) === 1);
        $this->assertTrue(count($grid[\Grid\Column\Column::class]) === 2);
        $this->assertTrue(count($grid[\Grid\Plugin\HeaderPlugin::class]) === 1);
        $this->assertTrue(count($grid[\Grid\Util\Hydrator::class]) === 1);
        $this->assertTrue(isset($grid[InputsInterface::class]));
        foreach ($grid[InputsInterface::class] as $object) {
            $this->assertTrue(count($object->getInputs()) > 0);
        }

        $body = [];
        foreach ($grid->getData()as $row) {
            if ($row->isBody()) {
                $body[] = $row;
            }
        }
        $this->assertTrue(count($body) === 1);


        $html = $grid->render();

        $this->assertTrue(strpos($html, 'name="grid[grid-id][searchable][id]"') !== false);
        $this->assertTrue(strpos($html, 'name="grid[grid-id][selectable][id]"') !== false);

        try {
            $grid = StaticFactory::factory(
                [
                    'Some random class'
                ]
            );
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $grid = StaticFactory::factory(
                [
                    [
                        'class' => \Grid\Source\ArraySource::class,
                    ],
                ]
            );
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        $grid = StaticFactory::factory(
            [
                [
                    'callback' => [\GridTest\GridTest::class, 'createPlugin'],
                    'options' => [
                        'name' => 'id',
                        'label' => '#',
                        'dbFields' => 'id',
                        'sortable' => true,
                    ]
                ],
            ]
        );
        $this->assertTrue(count($grid->getColumns()) === 1);

        $grid = StaticFactory::factory(
            [
                [
                    'callback' => [$this, 'createPlugin'],
                    'options' => [
                        'name' => 'id',
                        'label' => '#',
                        'dbFields' => 'id',
                        'sortable' => true,
                    ]
                ],
            ]
        );
        $this->assertTrue(count($grid->getColumns()) === 1);

        try {
            $grid = StaticFactory::factory(
                [
                    [
                        'callback' => ['NO SUCH CLASS', 'createPlugin'],
                        'options' => [
                            'name' => 'id',
                            'label' => '#',
                            'dbFields' => 'id',
                            'sortable' => true,
                        ]
                    ],
                ]
            );
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $grid = StaticFactory::factory(
                [
                    [
                        'callback' => [],
                    ],
                ]
            );
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        
        try {
            $grid = StaticFactory::factory(
                [
                    null,
                ]
            );
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

//        $grid = StaticFactory::factory(
//            [
//                $instance = new Grid,
//            ]
//        );
//        $this->assertTrue($grid === $instance);

        
    }

    public function createPlugin($config)
    {
        return new \Grid\Column\Column($config);
    }

    public function testFullGrid()
    {
        $_SERVER['REQUEST_URI'] = '/';
        $products = array(
          array('productId' => '1','userId' => '2','platformId' => '3','platformKey' => 'B01CCRDHF8','url' => 'http://www.amazon.co.uk/Lightning-Syncwire-Braided-iPhone-Charger/dp/B01CCRDHF8%3Fpsc%3D1%26SubscriptionId%3DAKIAJ27SSS6GIPTR5C7Q%26tag%3D2kblater-20%26linkCode%3Dsp1%26camp%3D2025%26creative%3D165953%26creativeASIN%3DB01CCRDHF8','title' => 'Lightning Cable Syncwire Nylon Braided iPhone Charger - [Apple MFi Certified] Lifetime Warranty Series- for iPhone 6S Plus 6 Plus SE 5S 5C 5, iPad 2 3 4 Mini, iPad Pro Air, iPod - 3.3ft/1m Space Gray [Upgraded Version]','price' => '699','priceCurrency' => '£6.99','lastPrice' => '700','lastPriceCurrency' => '£6.99','initialPrice' => '699','initialPriceCurrency' => '£6.99','quantity' => '15','image' => 'http://ecx.images-amazon.com/images/I/415YqCeUkAL._SL75_.jpg','lastPriceUpdate' => '2016-09-05 22:00:01','lastPriceChange' => '2016-09-01 20:03:34','lastPlatformsSync' => '2016-08-29 22:24:35','syncWithPlatforms' => NULL,'isDeleted' => NULL,'timestamp' => '2016-09-05 22:00:01'),
          array('productId' => '3','userId' => '2','platformId' => '3','platformKey' => 'B0158HR002','url' => 'http://www.amazon.co.uk/HP-DeskJet-3630-Printer-Start/dp/B0158HR002%3Fpsc%3D1%26SubscriptionId%3DAKIAJ27SSS6GIPTR5C7Q%26tag%3D2kblater-20%26linkCode%3Dsp1%26camp%3D2025%26creative%3D165953%26creativeASIN%3DB0158HR002','title' => 'HP DeskJet 3630 All-in-One Printer with Start Up Inks','price' => '2999','priceCurrency' => '£29.99','lastPrice' => '3000','lastPriceCurrency' => '£29.99','initialPrice' => '2999','initialPriceCurrency' => '£29.99','quantity' => '15','image' => 'http://ecx.images-amazon.com/images/I/31XaHhC6YaL._SL75_.jpg','lastPriceUpdate' => '2016-09-05 22:00:04','lastPriceChange' => '2016-09-01 20:03:38','lastPlatformsSync' => '2016-08-29 22:33:58','syncWithPlatforms' => NULL,'isDeleted' => NULL,'timestamp' => '2016-09-05 22:00:04'),
          array('productId' => '4','userId' => '2','platformId' => '3','platformKey' => 'B015R21FJA','url' => 'http://www.amazon.co.uk/Mpow-Waterproof-Underwater-Responsive-Transparent/dp/B015R21FJA%3Fpsc%3D1%26SubscriptionId%3DAKIAJ27SSS6GIPTR5C7Q%26tag%3D2kblater-20%26linkCode%3Dsp1%26camp%3D2025%26creative%3D165953%26creativeASIN%3DB015R21FJA','title' => 'Mpow IPX8 Waterproof Case, Universal Durable Underwater Dry Bag, Touch Responsive Transparent Windows,Watertight Sealed System for iPhone 6s,6s plus,5,SE,Samsung Galaxy S7 / S7 Edage,Samsung Note 3 and Other Smartphone; Waterproof Bag for Boating/Hiking/Swimming/Diving(White)','price' => '559','priceCurrency' => '£5.59','lastPrice' => '560','lastPriceCurrency' => '£5.59','initialPrice' => '559','initialPriceCurrency' => '£5.59','quantity' => '15','image' => 'http://ecx.images-amazon.com/images/I/41E7Xcb%2BOnL._SL75_.jpg','lastPriceUpdate' => '2016-09-05 22:00:06','lastPriceChange' => '2016-09-01 20:03:42','lastPlatformsSync' => '2016-08-29 22:35:09','syncWithPlatforms' => NULL,'isDeleted' => NULL,'timestamp' => '2016-09-05 22:00:06'),
          array('productId' => '5','userId' => '2','platformId' => '3','platformKey' => 'B00LFIXC8I','url' => 'http://www.amazon.co.uk/Anker-Reader-RS-MMC-Support-Warranty/dp/B00LFIXC8I%3FSubscriptionId%3DAKIAJ27SSS6GIPTR5C7Q%26tag%3D2kblater-20%26linkCode%3Dsp1%26camp%3D2025%26creative%3D165953%26creativeASIN%3DB00LFIXC8I','title' => 'Anker USB 3.0 Card Reader 8-in-1 for SDXC, SDHC, SD, MMC, RS-MMC, Micro SDXC, Micro SD, Micro SDHC Card, Support UHS-I Cards, 18 Months Warranty','price' => '899','priceCurrency' => '£8.99','lastPrice' => '900','lastPriceCurrency' => '£8.99','initialPrice' => '899','initialPriceCurrency' => '£8.99','quantity' => '15','image' => 'http://ecx.images-amazon.com/images/I/31beMvaPpfL._SL75_.jpg','lastPriceUpdate' => '2016-09-05 22:00:08','lastPriceChange' => '2016-09-01 20:03:45','lastPlatformsSync' => '2016-08-29 22:35:30','syncWithPlatforms' => NULL,'isDeleted' => NULL,'timestamp' => '2016-09-05 22:00:08'),
          array('productId' => '6','userId' => '2','platformId' => '3','platformKey' => 'B00VH84L5E','url' => 'http://www.amazon.co.uk/Anker-Charger-PowerDrive-Samsung-Android/dp/B00VH84L5E%3Fpsc%3D1%26SubscriptionId%3DAKIAJ27SSS6GIPTR5C7Q%26tag%3D2kblater-20%26linkCode%3Dsp1%26camp%3D2025%26creative%3D165953%26creativeASIN%3DB00VH84L5E','title' => 'Anker Car Charger 24W / 4.8A Dual USB Travel Car Charger PowerDrive 2 for iPhone 6 / 6 Plus/ 6s / 6s Plus, iPad Pro / Air 2 / mini 3, Samsung Galaxy Note Series, S Series & Edge Models; LG G4 / G5; Google Nexus 5X 6P; and Other iOS and Android Devices (Black)','price' => '699','priceCurrency' => '£6.99','lastPrice' => '500','lastPriceCurrency' => '£5.00','initialPrice' => '699','initialPriceCurrency' => '£6.99','quantity' => '15','image' => 'http://ecx.images-amazon.com/images/I/41knyurzvbL._SL75_.jpg','lastPriceUpdate' => '2016-09-05 22:00:11','lastPriceChange' => '2016-09-02 12:00:15','lastPlatformsSync' => '2016-08-29 22:35:53','syncWithPlatforms' => NULL,'isDeleted' => NULL,'timestamp' => '2016-09-05 22:00:11'),
          array('productId' => '7','userId' => '2','platformId' => '3','platformKey' => 'B012SRLKZ6','url' => 'http://www.amazon.co.uk/Anker-PowerCore-Recharges-Portable-Chargers-Black/dp/B012SRLKZ6%3FSubscriptionId%3DAKIAJ27SSS6GIPTR5C7Q%26tag%3D2kblater-20%26linkCode%3Dsp1%26camp%3D2025%26creative%3D165953%26creativeASIN%3DB012SRLKZ6','title' => 'Anker PowerCore+ 13400 (Recharges 2X Faster than Normal Portable Chargers) Premium Aluminum 13400mAh External Battery Charger with Leading 4.8A Output for iPhone, iPad, Samsung and More','price' => '3099','priceCurrency' => '£30.99','lastPrice' => '700','lastPriceCurrency' => '£6.99','initialPrice' => '3099','initialPriceCurrency' => '£30.99','quantity' => '15','image' => 'http://ecx.images-amazon.com/images/I/41ejmtLPDLL._SL75_.jpg','lastPriceUpdate' => '2016-09-05 21:00:40','lastPriceChange' => '2016-09-01 20:03:51','lastPlatformsSync' => '2016-08-29 22:36:21','syncWithPlatforms' => NULL,'isDeleted' => NULL,'timestamp' => '2016-09-05 21:00:40'),
          array('productId' => '8','userId' => '2','platformId' => '3','platformKey' => 'B01FJGI9JI','url' => 'http://www.amazon.co.uk/Lightning-LifeProof-Syncwire-Braided-Charger/dp/B01FJGI9JI%3Fpsc%3D1%26SubscriptionId%3DAKIAJ27SSS6GIPTR5C7Q%26tag%3D2kblater-20%26linkCode%3Dsp1%26camp%3D2025%26creative%3D165953%26creativeASIN%3DB01FJGI9JI','title' => 'Lightning Cable [Fit LifeProof Case] Syncwire Nylon Braided iPhone Charger - [Apple MFi Certified] Lifetime Guarantee Series - for iPhone 6S Plus 6 Plus SE 5S 5C 5, iPad Mini 2 3 4, iPad Pro Air 2, iPod - Zinc-Alloy Connector - 1m Space Gray','price' => '1099','priceCurrency' => '£10.99','lastPrice' => '700','lastPriceCurrency' => '£6.99','initialPrice' => '1099','initialPriceCurrency' => '£10.99','quantity' => '15','image' => 'http://ecx.images-amazon.com/images/I/41-LGoyLKqL._SL75_.jpg','lastPriceUpdate' => '2016-09-05 21:00:42','lastPriceChange' => '2016-09-01 20:03:55','lastPlatformsSync' => '2016-08-29 22:37:10','syncWithPlatforms' => NULL,'isDeleted' => NULL,'timestamp' => '2016-09-05 21:00:42'),
          array('productId' => '9','userId' => '2','platformId' => '3','platformKey' => 'B015PC7R6W','url' => 'http://www.amazon.co.uk/Lightning-Syncwire-Braided-iPhone-Charger/dp/B015PC7R6W%3Fpsc%3D1%26SubscriptionId%3DAKIAJ27SSS6GIPTR5C7Q%26tag%3D2kblater-20%26linkCode%3Dsp1%26camp%3D2025%26creative%3D165953%26creativeASIN%3DB015PC7R6W','title' => 'Lightning Cable Syncwire Nylon Braided Rose Gold iPhone Charger - [Apple MFi Certified] Lifetime Warranty Series- for iPhone 6S Plus 6 Plus SE 5S 5C 5, iPad 2 3 4 Mini, iPad Pro Air, iPod - Aluminum Connector - 3.3ft/1m [Upgraded Version]','price' => '799','priceCurrency' => '£7.99','lastPrice' => '700','lastPriceCurrency' => '£6.99','initialPrice' => '799','initialPriceCurrency' => '£7.99','quantity' => '15','image' => 'http://ecx.images-amazon.com/images/I/41JM3bw%2BsIL._SL75_.jpg','lastPriceUpdate' => '2016-09-05 21:00:44','lastPriceChange' => '2016-09-01 20:04:07','lastPlatformsSync' => '2016-08-29 22:37:31','syncWithPlatforms' => NULL,'isDeleted' => NULL,'timestamp' => '2016-09-05 21:00:44'),
          array('productId' => '10','userId' => '2','platformId' => '3','platformKey' => 'B00UBLOJJG','url' => 'http://www.amazon.co.uk/Anker-Lightning-Connector-Certified-Ultra-High-Black/dp/B00UBLOJJG%3Fpsc%3D1%26SubscriptionId%3DAKIAJ27SSS6GIPTR5C7Q%26tag%3D2kblater-20%26linkCode%3Dsp1%26camp%3D2025%26creative%3D165953%26creativeASIN%3DB00UBLOJJG','title' => 'iPhone Charger, Anker 6ft Nylon Braided USB iPhone Cable with Lightning Connector [Apple MFi Certified] Ultra-High Lifespan Sync and Charge Cable for iPhone 6/ 6 Plus/ 6s, iPad Air 2, iPad Pro and More (Space Gray)','price' => '695','priceCurrency' => '£6.95','lastPrice' => '500','lastPriceCurrency' => '£5.00','initialPrice' => '695','initialPriceCurrency' => '£6.95','quantity' => '15','image' => 'http://ecx.images-amazon.com/images/I/51wZD7BWn-L._SL75_.jpg','lastPriceUpdate' => '2016-09-05 21:00:46','lastPriceChange' => '2016-09-02 12:00:28','lastPlatformsSync' => '2016-08-29 22:37:50','syncWithPlatforms' => NULL,'isDeleted' => NULL,'timestamp' => '2016-09-05 21:00:46'),
          array('productId' => '11','userId' => '2','platformId' => '3','platformKey' => 'B00J0S6QZ6','url' => 'http://www.amazon.co.uk/Lightning-Syncwire-Certified-Lifetime-Guarantee-White-3-3ft-1m/dp/B00J0S6QZ6%3Fpsc%3D1%26SubscriptionId%3DAKIAJ27SSS6GIPTR5C7Q%26tag%3D2kblater-20%26linkCode%3Dsp1%26camp%3D2025%26creative%3D165953%26creativeASIN%3DB00J0S6QZ6','title' => 'Lightning Cable Syncwire Apple iPhone Charger -[Apple MFi Certified] Lifetime Guarantee Series- for iPhone 6S Plus 6 Plus SE 5S 5C 5, iPad 2 3 4 Mini, iPad Pro Air 2, iPod - 3.3ft/1m White','price' => '549','priceCurrency' => '£5.49','lastPrice' => '149','lastPriceCurrency' => '£1.49','initialPrice' => '549','initialPriceCurrency' => '£5.49','quantity' => '15','image' => 'http://ecx.images-amazon.com/images/I/41oBQFXK5uL._SL75_.jpg','lastPriceUpdate' => '2016-09-05 21:00:48','lastPriceChange' => '2016-09-04 00:00:23','lastPlatformsSync' => '2016-08-29 22:38:09','syncWithPlatforms' => NULL,'isDeleted' => NULL,'timestamp' => '2016-09-05 21:00:48'),
          array('productId' => '12','userId' => '2','platformId' => '3','platformKey' => 'B005QI1A8C','url' => 'http://www.amazon.co.uk/Anker-PowerCore-mini-Aluminum-Lipstick-Sized-Black/dp/B005QI1A8C%3Fpsc%3D1%26SubscriptionId%3DAKIAJ27SSS6GIPTR5C7Q%26tag%3D2kblater-20%26linkCode%3Dsp1%26camp%3D2025%26creative%3D165953%26creativeASIN%3DB005QI1A8C','title' => 'Anker PowerCore+ mini (3350mAh Premium Aluminum Portable Charger) Lipstick-Sized External Battery Power Bank for iPhone 6 / 6 Plus, iPad Air 2 / mini 3, Galaxy S6 / S6 Edge and More (Black)','price' => '1099','priceCurrency' => '£10.99','lastPrice' => '1044','lastPriceCurrency' => '£10.44','initialPrice' => '1099','initialPriceCurrency' => '£10.99','quantity' => '15','image' => 'http://ecx.images-amazon.com/images/I/417yx2GOM4L._SL75_.jpg','lastPriceUpdate' => '2016-09-04 20:01:06','lastPriceChange' => '2016-09-03 19:00:41','lastPlatformsSync' => '2016-08-29 22:38:35','syncWithPlatforms' => NULL,'isDeleted' => NULL,'timestamp' => '2016-09-04 20:01:06'),
          array('productId' => '13','userId' => '2','platformId' => '3','platformKey' => 'B00P8SY7HQ','url' => 'http://www.amazon.co.uk/Anker-5200mAh-Portable-External-Technology-Black/dp/B00P8SY7HQ%3Fpsc%3D1%26SubscriptionId%3DAKIAJ27SSS6GIPTR5C7Q%26tag%3D2kblater-20%26linkCode%3Dsp1%26camp%3D2025%26creative%3D165953%26creativeASIN%3DB00P8SY7HQ','title' => 'Anker Astro E1 5200mAh Ultra Compact Portable Charger  External Battery Power Bank with PowerIQ Technology for iPhone, iPad, Samsung, Nexus, HTC and More (Black)','price' => '1190','priceCurrency' => '£11.90','lastPrice' => '800','lastPriceCurrency' => '£8.00','initialPrice' => '1190','initialPriceCurrency' => '£11.90','quantity' => '15','image' => 'http://ecx.images-amazon.com/images/I/315eH7uWPTL._SL75_.jpg','lastPriceUpdate' => '2016-09-04 20:01:08','lastPriceChange' => '2016-09-02 12:00:37','lastPlatformsSync' => '2016-08-29 22:39:00','syncWithPlatforms' => NULL,'isDeleted' => NULL,'timestamp' => '2016-09-04 20:01:08'),
          array('productId' => '14','userId' => '2','platformId' => '3','platformKey' => 'B00K4VQZCM','url' => 'http://www.amazon.co.uk/Certified-Anker-Lightning-Compact-Connector-White/dp/B00K4VQZCM%3FSubscriptionId%3DAKIAJ27SSS6GIPTR5C7Q%26tag%3D2kblater-20%26linkCode%3Dsp1%26camp%3D2025%26creative%3D165953%26creativeASIN%3DB00K4VQZCM','title' => '[Apple MFI Certified] Anker Lightning to USB Cable 3ft / 0.9m High Life Span Cable with Compact Connector Head for iPhone 6s / 6splus / 6 / 6 Plus / SE / 5s / 5c / 5, iPad / Air / Air2 / mini / mini2 / mini3, iPad 4th gen, iPod touch 5th gen, and iPod nano 7th gen (White)','price' => '521','priceCurrency' => '£5.21','lastPrice' => '3001','lastPriceCurrency' => '£29.99','initialPrice' => '521','initialPriceCurrency' => '£5.21','quantity' => '15','image' => 'http://ecx.images-amazon.com/images/I/31-9dAokTOL._SL75_.jpg','lastPriceUpdate' => '2016-09-04 20:01:10','lastPriceChange' => '2016-09-01 20:32:10','lastPlatformsSync' => '2016-08-29 22:39:23','syncWithPlatforms' => NULL,'isDeleted' => NULL,'timestamp' => '2016-09-04 20:01:10'),
          array('productId' => '15','userId' => '2','platformId' => '3','platformKey' => 'B015S8RCDQ','url' => 'http://www.amazon.co.uk/Syncwire-Universal-Magnetic-Lifetime-Smartphones/dp/B015S8RCDQ%3Fpsc%3D1%26SubscriptionId%3DAKIAJ27SSS6GIPTR5C7Q%26tag%3D2kblater-20%26linkCode%3Dsp1%26camp%3D2025%26creative%3D165953%26creativeASIN%3DB015S8RCDQ','title' => '[Top Rated Gadget] Car Phone Holder Syncwire Universal Air Vent Magnetic Car Mount Cradle -Lifetime Warranty Series- for Apple iPhone 6S Plus 6 Plus SE 5S 5C 5, Samsung Galaxy S7 S6 Edge, Nexus, Smartphones and More','price' => '579','priceCurrency' => '£5.79','lastPrice' => '3001','lastPriceCurrency' => '£29.99','initialPrice' => '579','initialPriceCurrency' => '£5.79','quantity' => '15','image' => 'http://ecx.images-amazon.com/images/I/41RgXitsgSL._SL75_.jpg','lastPriceUpdate' => '2016-09-04 20:01:12','lastPriceChange' => '2016-09-01 20:32:13','lastPlatformsSync' => '2016-08-29 22:39:39','syncWithPlatforms' => NULL,'isDeleted' => NULL,'timestamp' => '2016-09-04 20:01:12'),
          array('productId' => '16','userId' => '2','platformId' => '3','platformKey' => 'B011KPRE1G','url' => 'http://www.amazon.co.uk/Syncwire-International-Interchangeable-Lifetime-Smartphone/dp/B011KPRE1G%3Fpsc%3D1%26SubscriptionId%3DAKIAJ27SSS6GIPTR5C7Q%26tag%3D2kblater-20%26linkCode%3Dsp1%26camp%3D2025%26creative%3D165953%26creativeASIN%3DB011KPRE1G','title' => 'USB Charger Plug Syncwire 4-Port Wall Charger with UK EU US International Travel Adaptor (Interchangeable) -Lifetime Warranty Series- 6.8A/34W for Apple iPhone iPad, Samsung Galaxy, Smartphone, Tablet, Power Bank - White [UL Certification]','price' => '1399','priceCurrency' => '£13.99','lastPrice' => '1099','lastPriceCurrency' => '£10.99','initialPrice' => '1399','initialPriceCurrency' => '£13.99','quantity' => '15','image' => 'http://ecx.images-amazon.com/images/I/41ezVrScojL._SL75_.jpg','lastPriceUpdate' => '2016-09-04 20:01:14','lastPriceChange' => '2016-09-03 14:00:48','lastPlatformsSync' => '2016-08-29 22:39:54','syncWithPlatforms' => NULL,'isDeleted' => NULL,'timestamp' => '2016-09-04 20:01:14'),
          array('productId' => '17','userId' => '2','platformId' => '3','platformKey' => 'B00U10GB20','url' => 'http://www.amazon.co.uk/Lightning-Syncwire-Certified-Lifetime-Guarantee/dp/B00U10GB20%3Fpsc%3D1%26SubscriptionId%3DAKIAJ27SSS6GIPTR5C7Q%26tag%3D2kblater-20%26linkCode%3Dsp1%26camp%3D2025%26creative%3D165953%26creativeASIN%3DB00U10GB20','title' => 'Lightning Cable Syncwire 6.5ft/2m iPhone Charger -[Apple MFi Certified] Lifetime Guarantee Series- for iPhone 6S Plus 6 Plus SE 5S 5C 5, iPad 2 3 4 Mini, iPad Pro Air, iPod - White','price' => '799','priceCurrency' => '£7.99','lastPrice' => '3001','lastPriceCurrency' => '£29.99','initialPrice' => '799','initialPriceCurrency' => '£7.99','quantity' => '15','image' => 'http://ecx.images-amazon.com/images/I/418CpuTdEgL._SL75_.jpg','lastPriceUpdate' => '2016-09-04 20:01:16','lastPriceChange' => '2016-09-01 20:32:19','lastPlatformsSync' => '2016-08-29 22:41:19','syncWithPlatforms' => NULL,'isDeleted' => NULL,'timestamp' => '2016-09-04 20:01:16')
        );

        
        $config = [];
        $config[] = [
            'class' => \Grid\Column\Column::class,
            'options' => [
                'name' => 'productId',
                'label' => '#',
                'dbFields' => 'productId',
                'sortable' => true,
            ]
        ];
        $config[] = [
            'class' => \Grid\Column\Column::class,
            'options' => [
                'name' => 'userId',
                'label' => 'User',
                'dbFields' => 'userId',
                'sortable' => true,
                'selectable' => true,
            ]
        ];
        $config[] = [
            'class' => \Grid\Column\Column::class,
            'options' => [
                'name' => 'platformId',
                'label' => 'Platform',
                'dbFields' => 'platformId',
                'sortable' => true,
                'selectable' => true,
            ]
        ];
        $config[] = [
            'class' => \Grid\Column\Column::class,
            'options' => [
                'name' => 'platformKey',
                'label' => 'Platform key',
                'dbFields' => 'platformKey',
                'sortable' => true,
                'selectable' =>  true,
            ]
        ];
        $config[] = [
            'class' => \Grid\Column\Column::class,
            'options' => [
                'name' => 'title',
                'label' => 'Title',
                'dbFields' => 'title',
                'sortable' => true,
                'searchable' => true,
            ]
        ];
        $config[] = [
            'class' => \Grid\Column\Column::class,
            'options' => [
                'name' => 'priceCurrency',
                'label' => 'Price',
                'dbFields' => 'priceCurrency',
                'sortable' => true,
            ]
        ];
        $config[] = [
            'class' => \Grid\Column\Column::class,
            'options' => [
                'name' => 'lastPriceCurrency',
                'label' => 'Last price',
                'dbFields' => 'lastPriceCurrency',
                'sortable' => true,
            ]
        ];
        $config[] = [
            'class' => \Grid\Column\Column::class,
            'options' => [
                'name' => 'initialPriceCurrency',
                'label' => 'Init price',
                'dbFields' => 'initialPriceCurrency',
                'sortable' => true,
            ]
        ];
        $config[] = [
            'class' => \Grid\Column\Column::class,
            'options' => [
                'name' => 'quantity',
                'label' => 'Qty',
                'dbFields' => 'quantity',
                'sortable' => true,
            ]
        ];
        $config[] = [
            'class' => \Grid\Column\Column::class,
            'options' => [
                'name' => 'image',
                'label' => 'Image',
                'dbFields' => 'image',
                'dataType' => \Grid\DataType\Image::class,
            ]
        ];
        $config[] = [
            'class' => \Grid\Column\Column::class,
            'options' => [
                'name' => 'lastPriceUpdate',
                'label' => 'Last update',
                'dbFields' => 'lastPriceUpdate',
                'dataType' => \Grid\DataType\Date::class,
                'sortable' => true,
            ]
        ];
        $config[] = [
            'class' => \Grid\Column\Column::class,
            'options' => [
                'name' => 'lastPriceChange',
                'label' => 'Last price change',
                'dbFields' => 'lastPriceChange',
                'dataType' => \Grid\DataType\TimeAgo::class,
            ]
        ];
        $config[] = [
            'class' => \Grid\Source\ArraySource::class,
            'options' => [
                'driver' => $products,
                'order' => ['productId' => 'ASC'],
                'limit' => 5,
            ]
        ];
        
        $config[] = [
            'class' => \Grid\Util\Links::class,
            'options' => [
                'params' => [
                    'grid' => [
                        'grid-id' => [
                            'sortable' =>[
                                'lastPriceCurrency' => 'asc'
                            ],
                            'page' => 2
                        ]
                    ]
                ],
            ]
        ];

        //grid[grid-id][sortable][platformKey]=asc
        $config[] = [
            'class' => \Grid\Plugin\LinksPlugin::class,
            'options' => [
                'column'        => 'platformKey',
                'links' => [
                    [
                        'uri'           => 'https://www.amazon.co.uk/dp/:platformKey',
                        'uriParameters' => [
                            'platformKey' => 'platformKey',
                        ],
                        'attributes' => [
                            'class' => 'btn btn-default',
                            'target' => '_blank'
                        ],
                    ],
                ]
            ],
        ];
        $config[] = [
            'class' => \Grid\Plugin\ColumnFilterablePlugin::class,
            'options' => [
                'markMatches' => true,
            ]
        ];
        $config[] = [
            'class' => \Grid\Plugin\ColumnSortablePlugin::class,
            'options' => [
                
            ]
        ];
        
        $config[] = \Grid\Renderer\HtmlRenderer::class;
        $config[] = [
            'class' => \Grid\Plugin\PaginationPlugin::class,
            'options' => [
                'itemsPerPage' => 5,
            ]
        ];
        $config[] = [
            'class' => \Grid\Plugin\HeaderPlugin::class,
            'options' => [
                'position' => \Grid\Plugin\HeaderPlugin::POSITION_BOTH,
            ]
        ];
        $grid = \Grid\Factory\StaticFactory::factory($config);

        $html = $grid->render();
        $html = urldecode($html);
//        $url = '?grid%5Bgrid-id%5D%5Bsortable%5D%5BplatformKey%5D=asc&grid%5Bgrid-id%5D%5Bsortable%5D%5Btitle%5D=desc';
//        echo urldecode($url);die;
        $this->assertTrue(strpos($html, 'grid[grid-id][sortable][title]=asc') !== false);
        $this->assertTrue(strpos($html, 'grid[grid-id][sortable][lastPriceCurrency]=desc') !== false);
        $this->assertTrue(strpos($html, 'grid[grid-id][page]=3') !== false);


//        var_dump($grid[\Grid\Util\Links::class][0]->getParams());die;

//        echo $html;die;
    }
}
