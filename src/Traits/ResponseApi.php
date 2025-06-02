<?php

namespace Mohamedgcoder\ResponseApi\Traits;

trait ResponseApi
{
    private $data;
    private $pagination;
    private int $ec = 0;
    private int $code = 200;
    private string $status;
    private $exception;
    private $messages;

    public function setData($data): void
    {
        $this->data = $data;
        $this->setPagination();
    }

    public function setPagination(): void
    {
        $data = $this->data();

        try {
            $paginationData = [
                'path' => $data->path(),
                'total' => $data->total(),
                'perPage' => $data->perPage(),
                'currentPage' => $data->currentPage(),
                'lastPage' => $data->lastPage(),
            ];
        } catch (\Throwable $th) {
            $paginationData = [];
        }

        $this->pagination = $paginationData;
    }

    public function setException($exception)
    {
        $this->exception = $exception;
    }

    public function setEc($ec)
    {
        $this->ec = $ec;
    }

    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function data()
    {
        return $this->data;
    }

    public function pagination()
    {
        return $this->pagination;
    }

    public function exception()
    {
        return $this->exception;
    }

    public function ec()
    {
        return $this->ec;
    }

    public function messages()
    {
        return $this->messages;
    }

    public function code()
    {
        return $this->code;
    }

    public function apiResponse()
    {
        try {
            $dataSize = sizeof($this->data);
        } catch (\Throwable) {
            $dataSize = 1;
        }

        $dataSize = ($this->data() != null) ? $dataSize : 0;
        $status = (($dataSize >= 0 && $this->code == 200) || $this->code == 201) ? true : false;

        $this->messages = ($this->exception != null) ? [__('errors.some_thing_error')] : $this->messages;
        $this->messages = ($dataSize === 0) ? (($this->messages == null) ? [__('errors.no_data_to_view')] : $this->messages) : $this->messages;

        return response()->json([
            'code' => $this->code,
            'responseStatus' => $status,
            'messages' => $this->messages,
            'response' => [
                'dataLength' => $dataSize,
                'pagination' => $this->pagination,
                'data' => $this->data(),
            ],
            'error' => [
                'errorCode' => ($this->ec === 0) ? null : $this->ec,
                'line' => ($this->exception === null) ? null : $this->exception->getLine(),
                'errorMessage' => ($this->exception === null) ? null : $this->exception->getMessage(),
                'file' => ($this->exception === null) ? null : $this->exception->getFile(),
            ],
        ], $this->code);
    }
}
