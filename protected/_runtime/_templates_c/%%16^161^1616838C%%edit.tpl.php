<?php /* Smarty version 2.6.26, created on 2010-09-24 17:26:16
         compiled from system:news/edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'href', 'system:news/edit.tpl', 1, false),)), $this); ?>
<p><a <?php echo smarty_function_href(array('url' => "admin/news"), $this);?>
>Категории материалов</a>
<?php if (! is_null ( $this->_tpl_vars['cat'] )): ?> &gt; <a <?php echo smarty_function_href(array('url' => 'admin/news','catid' => $this->_tpl_vars['cat']['id']), $this);?>
><?php echo $this->_tpl_vars['cat']['name']; ?>
</a> <?php endif; ?>
 &gt; Правка материала</p>

<?php echo $this->_tpl_vars['form']->html(); ?>
