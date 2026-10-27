<?php

declare(strict_types=1);

const APP_TIMEZONE = 'Asia/Jakarta';
const DB_PATH = __DIR__ . '/storage/app.sqlite';
const RATE_CACHE_TTL = 60;
const RATE_STALE_TTL = 3600;
const MANDIRI_CONNECT_TIMEOUT = 5;
const MANDIRI_REQUEST_TIMEOUT = 10;

const MANDIRI_ENDPOINT = 'https://www.bankmandiri.co.id/kurs-display'
    . '?p_p_id=Exchange_Rate_Portlet_INSTANCE_m7rMvPoazf24'
    . '&p_p_lifecycle=2'
    . '&p_p_state=normal'
    . '&p_p_mode=view'
    . '&p_p_resource_id=calculateCurrency'
    . '&p_p_cacheability=cacheLevelPage';

const MANDIRI_PREFIX = '_Exchange_Rate_Portlet_INSTANCE_m7rMvPoazf24_';
const MANDIRI_USD_CODE = '204';
const MANDIRI_IDR_CODE = '61';

date_default_timezone_set(APP_TIMEZONE);
