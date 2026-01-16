<?php
interface NotificationSenderInterface {
    /**
     * @param array $job
     * @return array{success:bool,external_id:?string,error:?string,raw:?array}
     */
    public function send(array $job): array;
}
