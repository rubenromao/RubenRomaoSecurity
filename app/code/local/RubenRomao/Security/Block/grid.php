<?php

class Task_News_Block_Adminhtml_News_Grid extends Mage_Adminhtml_Block_Widget_Grid
{


    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tasknews/news')->getCollection();
        $newsAuthorsTable = Mage::getSingleton('core/resource')->getTableName('tasknews/news');

        $collection->getSelect()->join(array('authors' => $newsAuthorsTable),
            'main_table.news_id=authors.news_id',
            array());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $helper = Mage::helper('tasknews');

        $this->addColumn('news_id', array(
            'header' => $helper->__('News ID'),
            'index' => 'news_id'
        ));

        $this->addColumn('title', array(
            'header' => $helper->__('Title'),
            'index' => 'title',
            'type' => 'text',
        ));

        $this->addColumn('author', array(
            'header' => $helper->__('Author'),
            'index' => 'author',
            'type' => 'text',
        ));


        $this->addColumn('created', array(
            'header' => $helper->__('Created'),
            'index' => 'created',
            'type' => 'date',
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('news_id');
        $this->getMassactionBlock()->setFormFieldName('news');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => $this->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
        ));
        return $this;
    }

    public function getRowUrl($model)
    {
        return $this->getUrl('*/*/edit', array(
            'id' => $model->getId(),
        ));
    }
}