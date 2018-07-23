<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\web\JsExpression;
use yii\widgets\Pjax;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'My Yii Application';

$script = <<<SCRIPT
$(window).scroll(function () { 
   if ($(window).scrollTop() >= $(document).height() - $(window).height() - 10) {
     $('.ias-trigger').trigger('click');
   }
});
SCRIPT;

$this->registerJs($script);

$this->registerJs('
var $grid = $(\'.row\').masonry({
            itemSelector: \'none\', // select none at first
            percentPosition: true,
            stagger: 30,
            // nicer reveal transition
            visibleStyle: { transform: \'translateY(0)\', opacity: 1 },
            hiddenStyle: { transform: \'translateY(100px)\', opacity: 0 },
        });

        // get Masonry instance
        var msnry = $grid.data(\'masonry\');

        // initial items reveal
        $grid.imagesLoaded().progress(function() {
            $grid.removeClass(\'are-images-unloaded\');
            $grid.masonry( \'option\', { itemSelector: \'.item\' });
            var $items = $grid.find(\'.item\');
            $grid.masonry( \'appended\', $items );
        });

        var nextPenSlugs = [
            \'202252c2f5f192688dada252913ccf13\',
            \'a308f05af22690139e9a2bc655bfe3ee\',
            \'6c9ff23039157ee37b3ab982245eef28\',
        ];

        function getPenPath() {
            var slug = nextPenSlugs[this.loadCount];
            if (slug) {
                return \'https://s.codepen.io/desandro/debug/\' + slug;
            }
        }

        $grid.infiniteScroll({
            path: getPenPath,
            append: \'.item\',
            outlayer: msnry,
            status: \'.page-load-status\'
        });');
?>
<style>
    /*.item { width: 25%; }*/
    /*.item.w2 { width: 50%; }*/

    .item {

        /*background-color: #ffffff;*/
    }
    
    .form-wrapper {
        display: block;
        clear: both;
        position: relative;
        margin-bottom: 20px;
        bottom: 50px;
    }

    .ias-trigger {
        display: block;
        clear: both;
    }

</style>
<div class="site-index">

    <div class="jumbotron">
        <h3>Sample Masonry API!</h3>
    </div>

    <div class="body-content">

        <div class="row">
<!--            --><?php //\yii2masonry\yii2masonry::begin([
//                'id' => 'WgArbeiten',
//                'clientOptions' => [
//                    'itemSelector' => '.item'
//                ]
//            ]); ?>

<!--            --><?php //foreach ($dataProvider->getModels() as $key => $model) : ?>
<!--                <div class="images col-md-3">-->
<!--                    <p>-->
<!--                        <img src="--><?//= $model['image_url'] ?><!--" class="img-responsive"/>-->
<!--                    </p>-->
<!--                </div>-->
<!--            --><?php //endforeach; ?>



            <?php #\yii2masonry\yii2masonry::end(); ?>

            <?= ListView::widget([
                    'dataProvider' => $dataProvider,
                    'itemOptions' => ['class' => 'item col-md-3'],
                    'itemView' => '_view',
                    'pager' => [
                        'class' => \kop\y2sp\ScrollPager::className(),
                        'spinnerSrc' => 'https://raw.githubusercontent.com/kop/yii2-scroll-pager/v1.0.2/assets/infinite-ajax-scroll/images/loader.gif',
                        'spinnerTemplate' => '<div class="ias-spinner" style="text-align: center; display: block; clear: both;"><img src="{src}"/></div>'
                    ],
                ]);
            ?>
        </div>

    </div>
</div>
