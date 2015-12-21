<?php $this->layout ('layouts/page') ?>

<?php $this->start ('pageContent') ?>
<section class="content-header" id="vue-app-data-view">
    <div class="row">
        <div class="col-md-8">
            <h3 style="margin-top: 5px">
                <?= $this->e ($page_name) ?>
            </h3>
        </div>
        <div class="col-md-4">
            <div class="pull-right">
                <h3>Action Buttons</h3>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary" style='padding-top:15px'>
                    <div class="box-body" >
                        <h3>Page Content Comes here</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
</section>
<?php $this->stop () ?>
