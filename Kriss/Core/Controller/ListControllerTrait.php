<?php

namespace Kriss\Core\Controller;

trait ListControllerTrait {
    private function listAction() {
        $params = $this->router->getRouteParameters();
        $criteria = [];
        if (isset($params['id'])) $criteria = ['id' => (int)$params['id']];
        $data = $this->request->getQuery();
        if (isset($data['search'])) {
            $search = json_decode($data['search'], true);
            if (is_null($search)) $search = json_decode('"'.$data['search'].'"');
            $criteria = $search;
        }
        $orderBy = isset($data['order_by'])?$data['order_by']:null;
        $offset = isset($data['offset'])?$data['offset']:null;
        $limit = (int)(isset($data['limit'])?$data['limit']:24);
        $page = isset($data['page'])?$data['page']:1;
        if (is_null($offset)) { $offset = ($page-1)*$limit; }
        $this->viewModel->setOrderBy($orderBy);
        $this->viewModel->setOffset($offset);
        $this->viewModel->setLimit($limit);
        $this->viewModel->setCriteria($criteria);
    }
}
