/**
     * @param Request $request
     * @return string
     *
     * @cat TODO
     * @title TODO
     * @comment TODO
     */
    protected function <?=$actionName?>(Request $request)
    {
        self::setPageTitleAndBreadcrumb('<?=$pageName?>', []);
        return self::render('admin.<?=$groupName . '.'?><?=$pageName?>', []);
    }

    /*__ACTION_PLACEHOLDER__*/
