<?php

namespace App\Http\Controllers\Api\SupplierManage;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;

class SupplierManageController extends BaseController
{
    const RETAILER_TYPE = 1;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // 获取参数
        $page = $request->input('page', 1);
        $count = 20;
        $start = ($page - 1) * $count;

        $params = [
            'start' => $start,
            'count' => $count,
        ];

        $url = '/supplier/getSupplierList';
        $data = $this->get($url, $params);

        if (! empty($data['data']['supplier_list'])) {
            foreach ($data['data']['supplier_list'] as &$item) {
                $item['cooperate_type_cn'] = self::RETAILER_TYPE === $item['cooperate_type'] ? '网络分销' : '-';
                $item['status_cn'] = $this->statusCn($item['status']);
            }
        }

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 获取参数
        $supplier_no = $request->input('supplier_no');
        $supplier_name = $request->input('supplier_name');
        $base = intval($request->input('base'));
        $business_license_no = $request->input('business_license_no');
        $business_license = $request->input('business_license');
        $general_taxpayer_certification = $request->input('general_taxpayer_certification', []);
        $corporate_identity_card = $request->input('corporate_identity_card');
        $permit_of_opening_a_bank_account = $request->input('permit_of_opening_a_bank_account');
        $nearly_one_year_tax_payment_certificate = $request->input('nearly_one_year_tax_payment_certificate');
        $trademark_registration_certificate = $request->input('trademark_registration_certificate', []);
        $link_certification = $request->input('link_certification', []);
        $operation_authorization_certificate = $request->input('operation_authorization_certificate');
        $opening_bank_name = $request->input('opening_bank_name');
        $subbranch_name = $request->input('subbranch_name');
        $bank_account = $request->input('bank_account');
        $contact_name = $request->input('contact_name');
        $contact_phone_number = $request->input('contact_phone_number');
        $contact_email_address = $request->input('contact_email_address');
        $warehouse_location = $request->input('warehouse_location');
        $warehouse_contact_name = $request->input('warehouse_contact_name');
        $warehouse_contact_phone_number = $request->input('warehouse_contact_phone_number');
        $warehouse_detailed_address = $request->input('warehouse_detailed_address');
        $pick_up_type = intval($request->input('pick_up_type'));
        $country_code = $request->input('country_code');
        $account_name = $request->input('account_name');
        $phone_number = $request->input('phone_number');
        $status = $request->input('status');
        $password = $request->input('password');

        $this->unsetFullPath($business_license);
        $this->unsetFullPath($general_taxpayer_certification);
        $this->unsetFullPath($corporate_identity_card);
        $this->unsetFullPath($permit_of_opening_a_bank_account);
        $this->unsetFullPath($nearly_one_year_tax_payment_certificate);
        $this->unsetFullPath($trademark_registration_certificate);
        $this->unsetFullPath($link_certification);
        $this->unsetFullPath($operation_authorization_certificate);

        $params = [
            'supplier_no' => $supplier_no,
            'supplier_name' => $supplier_name,
            'base' => $base,
            'business_license_no' => $business_license_no,
            'business_license' => $business_license,
            'general_taxpayer_certification' => $general_taxpayer_certification,
            'corporate_identity_card' => $corporate_identity_card,
            'permit_of_opening_a_bank_account' => $permit_of_opening_a_bank_account,
            'nearly_one_year_tax_payment_certificate' => $nearly_one_year_tax_payment_certificate,
            'trademark_registration_certificate' => $trademark_registration_certificate,
            'link_certification' => $link_certification,
            'operation_authorization_certificate' => $operation_authorization_certificate,
            'opening_bank_name' => $opening_bank_name,
            'subbranch_name' => $subbranch_name,
            'bank_account' => $bank_account,
            'contact_name' => $contact_name,
            'contact_phone_number' => $contact_phone_number,
            'contact_email_address' => $contact_email_address,
            'warehouse_location' => $warehouse_location,
            'warehouse_contact_name' => $warehouse_contact_name,
            'warehouse_contact_phone_number' => $warehouse_contact_phone_number,
            'warehouse_detailed_address' => $warehouse_detailed_address,
            'country_code' => $country_code,
            'account_name' => $account_name,
            'phone_number' => $phone_number,
            'pick_up_type' => $pick_up_type,
            'status' => $status,
        ];

        if (! empty($password)) {
            $params['password'] = $password;
        }

        $url = '/supplier/editSupplierInfo';
        $data = $this->post($url, $params);

        return response()->json($data);
    }

    public function unsetFullPath(&$imgArr)
    {
        if (! empty($imgArr)) {
            foreach ($imgArr as &$item) {
                unset($item['path_full']);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $params = [
            'supplier_no' => $id,
        ];

        $url = '/supplier/getSupplierDetail';
        $data = $this->get($url, $params);

        if (! empty($data['data']['supplier_detail']['base'])) {
            $data['data']['supplier_detail']['base'] = strval($data['data']['supplier_detail']['base']);
        }

        if (! empty($data['data']['supplier_detail']['pick_up_type'])) {
            $data['data']['supplier_detail']['pick_up_type'] = strval($data['data']['supplier_detail']['pick_up_type']);
        }

        // 营业执照
        if (! empty($data['data']['supplier_detail']['business_license'])) {
            foreach ($data['data']['supplier_detail']['business_license'] as &$busItem) {
                $busItem['path_full'] = cdn_image($busItem['path'], 'M');
            }
        }

        // 一般纳税人资格证
        if (! empty($data['data']['supplier_detail']['general_taxpayer_certification'])) {
            foreach ($data['data']['supplier_detail']['general_taxpayer_certification'] as &$taxItem) {
                $taxItem['path_full'] = cdn_image($taxItem['path'], 'M');
            }
        }

        // 法人身份证
        if (! empty($data['data']['supplier_detail']['corporate_identity_card'])) {
            foreach ($data['data']['supplier_detail']['corporate_identity_card'] as &$cardItem) {
                $cardItem['path_full'] = cdn_image($cardItem['path'], 'M');
            }
        }

        // 银行账户许可证
        if (! empty($data['data']['supplier_detail']['permit_of_opening_a_bank_account'])) {
            foreach ($data['data']['supplier_detail']['permit_of_opening_a_bank_account'] as &$bankItem) {
                $bankItem['path_full'] = cdn_image($bankItem['path'], 'M');
            }
        }

        // 近一年纳税证明
        if (! empty($data['data']['supplier_detail']['nearly_one_year_tax_payment_certificate'])) {
            foreach ($data['data']['supplier_detail']['nearly_one_year_tax_payment_certificate'] as &$annualTaxItem) {
                $annualTaxItem['path_full'] = cdn_image($annualTaxItem['path'], 'M');
            }
        }

        // 商标注册证书
        if (! empty($data['data']['supplier_detail']['trademark_registration_certificate'])) {
            foreach ($data['data']['supplier_detail']['trademark_registration_certificate'] as &$markItem) {
                $markItem['path_full'] = cdn_image($markItem['path'], 'M');
            }
        }

        // 链路证明
        if (! empty($data['data']['supplier_detail']['link_certification'])) {
            foreach ($data['data']['supplier_detail']['link_certification'] as &$linkItem) {
                $linkItem['path_full'] = cdn_image($linkItem['path'], 'M');
            }
        }

        // 运营授权证明
        if (! empty($data['data']['supplier_detail']['operation_authorization_certificate'])) {
            foreach ($data['data']['supplier_detail']['operation_authorization_certificate'] as &$operationItem) {
                $operationItem['path_full'] = cdn_image($operationItem['path'], 'M');
            }
        }

        if (isset($data['data']['supplier_detail']['status'])) {
            $data['data']['supplier_detail']['status_cn'] = $this->getStatusCn($data['data']['supplier_detail']['status']);
        }

        return response()->json($data);
    }

    /**
     * 获取国家区号.
     * @return \Illuminate\Http\Response
     */
    public function getCountryCode()
    {
        $url = '/user/getCountryCode';
        $data = $this->get($url);

        return response()->json($data);
    }

    private function statusCn($status)
    {
        switch ($status) {
            case -1:
                return '审核不通过';
            case 0:
                return '待审核';
            case 1:
                return '合作中';
            default:
                return '-';
        }
    }

    private function getStatusCn($status)
    {
        switch ($status) {
            case -1:
                return 'refuse';
            case 0:
                return 'pendingAudit';
            case 1:
                return 'pass';
            default:
                return '-';
        }
    }
}
