export default function (ctx, params) {
  const skuInfo = params.product_sku
  const filterSku = skuInfo.filter(item => {
    const emptyPrice = item.product_sku_price === ''
    const emptyStock = item.sku_stock === ''

    return !emptyStock && !emptyPrice
  })

  if (params.brand_name_e === '' || params.brand_name_e === undefined) {
    ctx.$message.error('品牌不能为空')

    return false
  }
  if (params.goods_name === '' || params.goods_name === undefined) {
    ctx.$message.error('标题不能为空')

    return false
  }
  if (!params.product_property[0] || params.product_property[0].value_list[0].property_value === '' ||
    params.product_property[0].value_list[0].property_value === undefined) {
    ctx.$message.error('货号不能为空')

    return false
  }

  if (params.cover_image.length < 1) {
    ctx.$message.error('商品主图至少上传1张')

    return false
  }

  if (params.detail_image.length < 1) {
    ctx.$message.error('商品详情图至少上传1张')

    return false
  }

  if (params.description === '' || params.description === undefined) {
    ctx.$message.error('商品描述不能为空')

    return false
  }

  const intReg = /^[1-9]\d*|0$/
  const positiveIntReg = /^\+?[1-9][0-9]*$/
  const checkPrice = filterSku.every(item => positiveIntReg.test(item.product_sku_price))
  const checkStock = filterSku.every(item => intReg.test(item.sku_stock))
  const checkSkuPrice = filterSku.every(item => positiveIntReg.test(item.sku_price) || item.sku_price === '')

  if (!checkPrice) {
    ctx.$message.error('供货价应为正整数')

    return false
  }

  if (!checkStock) {
    ctx.$message.error('库存应为非负整数')

    return false
  }

  if (!checkSkuPrice) {
    ctx.$message.error('分销价应为正整数或者空')

    return false
  }

  return true
}
