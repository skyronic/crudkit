<?php $this->layout ('layouts/core') ?>
<?php $this->start ('body') ?>
<div class="wrapper">
    <header class="main-header">
        <a href="<?= $this->e($defaultUrl) ?>" class="logo"><?= $this->e($title) ?></a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <div class="navbar-custom-menu">
            </div>
        </nav>
    </header>
    <aside class="main-sidebar">
        <section class="sidebar">
            <ul class="sidebar-menu">
                <li class="header">PAGES</li>
                <?php foreach ($pages as $item): ?>
                    <li role="presentation"><a href="<?= $this->getPageUrl ($item) ?>"><i class="fa fa-book"></i> <?= $this->e($item->getName ()) ?></a></li>
                <?php endforeach ?>
            </ul>
        </section>
    </aside>

    <div class="content-wrapper">
        <?= $this->section ('pageContent') ?>
    </div>
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
        <b>Version</b> 0.5
      </div>
      <strong>Powered by <a href="http://crudkit.com">CrudKit</a>.</strong>
  </footer>
</div>
<?php $this->stop('body') ?>
