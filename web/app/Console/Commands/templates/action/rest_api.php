/**
     * @param Request $request
     * @return string
     *
     * @cat TODO
     * @title TODO
     * @comment TODO
     * @url-param TODO
     * @form-param TODO
     * @ret-val TODO
     * @ret-val TODO
     */
    protected function <?=$actionName?>(Request $request)
    {
        $result = Result::ok($request->input());

        if ($result->hasError()) {

        }

        return $result->data();
    }

    /*__ACTION_PLACEHOLDER__*/
