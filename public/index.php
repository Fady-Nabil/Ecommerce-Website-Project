<?php require_once("../resources/config.php"); ?>
<?php include(TEMPLATE_FRONT . DS . "header.php") ?>
    <!-- Page Content -->
    <div class="container">

        <div class="row">

            <!--categories-->
            <?php include(TEMPLATE_FRONT . DS . "side_nav.php") ?>

            <div class="col-md-9">

                <div class="row carousel-holder">
                    <!--slider -->
                    <?php include(TEMPLATE_FRONT . DS . "slider.php") ?>
                </div>

                <div class="row">
                    <?php get_products(); ?>
                </div>
                <!--row-->

            </div>

        </div>

    </div>
    <!-- /.container -->
<?php include(TEMPLATE_FRONT . DS . "footer.php") ?>
