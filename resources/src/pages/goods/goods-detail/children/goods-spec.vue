<template>
  <!-- 规格 -->
  <div class="table-row">
    <div class="row-label">规格</div>
    <div class="row-content row-content-bg">
      <!-- 颜色分类-->
      <color-dialog v-if="colorDialogVisible" ref="colorDialog"
                    @close="closeColorDialog"
                    :colorInfo="quoteInfo.sku_info.data[0].data_list"
      ></color-dialog>

      <!--上传组件-->
      <div class="upload-area" id="upload-color-area">
        <label id="upload-color-trigger" for="upload-color-files">上传图片</label>
        <input id="upload-color-files" type="file">
      </div>

      <div class="table-row" v-for="(item,index) in colorList" :key="index">
        <div class="row-label">
          <span v-if="index == 0">颜色分类</span>
        </div>

        <div class="row-content of_flex_align_center of_relative">
          <i v-if="index !== colorList.length - 1" class="color-icon el-icon-circle-close"
             @click="deleteInput(index,colorList)"
          ></i>
          <!--<el-checkbox v-model="item.checked"></el-checkbox>-->
          <el-input size="mini" class="color-item"
                    v-model="item.name"
                    @focus="openColorDialog($event, index, item.name)"
                    @change="colorChange($event, index)"
                    placeholder="增加颜色"
          >
          </el-input>
          <img :src="item.sku_image_list[0].path_full" alt="" class="color-img"
               v-if="item.sku_image_list && item.sku_image_list[0] && item.sku_image_list[0].path_full">
          <el-button @click="uploadImg($event, index)" size="mini" type="primary" v-else-if="index !== colorList.length - 1">上传图片</el-button>
          <el-button @click="deleteImg($event, index)" size="mini" type="primary"
                     v-if="item.sku_image_list && item.sku_image_list[0] && item.sku_image_list[0].path_full"
          >删除图片</el-button>
        </div>
      </div>

      <!--尺码 start-->
      <div class="table-row of_flex_align_center"  v-if="hasStandardSpec">
        <div class="row-label">尺码</div>
        <div class="row-content">
          <!--尺码一级分类-->
          <el-radio-group v-model="specRadio" @change="specChange">
            <el-radio :label="spec.code_name"  class="radio-item"
                      v-for="(spec,index) in quoteInfo.sku_info.data[1].data_list">
              {{spec.code_name}}
            </el-radio>
          </el-radio-group>
        </div>
      </div>

      <!--尺码二级分类-->
      <div class="table-row of_flex_align_center" v-if="hasStandardSpec">
        <div class="row-label"></div>
        <div class="row-content">
          <div class="checklist-bg">
            <el-checkbox :label="spec.name" v-for="(spec, index) in specSubList"
                         class="spec-sub-item"
                         v-model="spec.checked"
            ></el-checkbox>
          </div>
        </div>
      </div>

      <!--自定义尺码-->
      <div class="table-row">
        <div class="row-label"><span v-if="!hasStandardSpec">自定义尺码</span></div>
        <div class="row-content of_flex of_flex_wrap">
          <div class=" speclist" v-for="(item,index) in specSelfList" :key="index">
            <i v-if="index !== specSelfList.length - 1 " class="spec-icon el-icon-circle-close" @click="deleteInput(index,specSelfList)"></i>
            <el-checkbox v-if="0" v-model="specSelfList[index].checked" @change="selectSelfSpec($event, item.sku_spec)"></el-checkbox>
            <el-input size="mini spec-item"
                      v-model="specSelfList[index].sku_spec"
                      @focus="setLastSelfSpec(item.sku_spec)"
                      @change="selfChange($event,index)"
                      placeholder="自定义尺码">
            </el-input>
          </div>
        </div>
      </div>
      <!--尺码 end-->
      <!-- 销售规格-->
      <div class="table-row">
        <div class="row-label">销售规格</div>
        <div class="row-content of_flex_align_center">
          <el-input size="mini" class="fill-item" v-model.number="fillPrice" @change="isInt($event, 'fillPrice')" placeholder="填充分销价"></el-input>
          <el-input size="mini" class="fill-item" v-model.number="fillStock" @change="isInt($event, 'fillStock')" placeholder="填充库存" :disabled="isEdit"></el-input>
          <el-input size="mini" class="fill-item" v-model="fillSkuCode" placeholder="商品编码" v-if="!isEdit"></el-input>
          <el-input size="mini" class="fill-item" v-model="fillBarCode" placeholder="商品条形码" v-if="!isEdit"></el-input>
          <el-button type="primary" size="mini" @click="fill">确定填充</el-button>
        </div>
      </div>

      <div class="table-row">
        <div class="row-label"></div>
        <div class="row-content of_flex_align_center">
          <table class="of_table border">
            <thead>
            <th v-if="0"><el-checkbox v-model="allSku" @change="selectAllSku"></el-checkbox></th>
            <th style="width:60px;">颜色</th>
            <th style="width:60px;">尺码</th>
            <th style="width:80px;">供货价(HK$)</th>
            <th style="width:80px;">分销价(HK$)</th>
            <th>库存(件)</th>
            <th style="width:140px;">商品编码</th>
            <th style="width:140px;">商品条形码</th>
            </thead>
            <tbody>
              <tr v-for="(sku, index) in productSku" :key="index" v-if="allColor.length > 0 && allSpec.length > 0"
              >
                <td v-if="0"><el-checkbox v-model="sku.checked" @change="selectSku($event,sku.sku_spec)"></el-checkbox></td>
                <td :rowspan="rowspan" v-if="index % rowspan === 0">{{sku.sku_color}}
                </td>
                <td :data-color="sku.sku_color">{{sku.sku_spec}}</td>
                <td>
                  <el-input placeholder="供货价" class="table-input" v-model="sku.product_sku_price">
                  </el-input>
                </td>
                <td>
                  <el-input placeholder="分销价" class="table-input" v-model="sku.sku_price">
                  </el-input>
                </td>
                <td style="width:80px;" :class="{disabled:isEdit}">
                  <el-input placeholder="库存" class="table-input" :disabled="isEdit" v-model="sku.sku_stock">
                  </el-input>
                </td>
                <td :class="{disabled:isEdit}">
                  <el-input placeholder="商品编码" class="table-input" v-model="sku.sku_code" :disabled="isEdit" :title="sku.sku_code"></el-input>
                </td>
                <td class="disabled" :title="barCode">{{barCode}}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- 价格及总库存-->
      <div class="table-row">
        <div class="row-label">价格及总库存</div>
        <div class="row-content of_flex_align_center">
          <table class="of_table border">
            <thead>
            <th style="width:120px;">官方货币单位</th>
            <th style="width:120px">官方价格({{currencyInfo ? currencyInfo.currency_sign : ''}})</th>
            <th style="width:100px">供货价(HK$)</th>
            <th style="width:100px">分销价(HK$)</th>
            <th>总数量</th>
            <th>商品条形码</th>
            </thead>
            <tbody>
            <tr>
              <td style="padding:4px 0;">
                <el-select v-model="currency" placeholder="货币单位" class="table-select" @change="changeCurrency">
                  <el-option
                      v-for="(item,index) in quoteInfo.currency_info"
                      :key="index"
                      :label="item.currency_name"
                      :value="item.currency">
                  </el-option>
                </el-select>
              </td>
              <td :class="{'disabled':isEdit}">
                <el-input placeholder="官方价格" class="table-input" v-model.number="officialPrice" v-if="!isEdit"></el-input>
                <span v-if="isEdit">{{officialPrice}}</span>
              </td>
              <td class="disabled">{{price}}</td>
              <td class="disabled">{{salePrice}}</td>
              <td class="disabled">{{stock}}</td>
              <td :class="{disabled:isEdit}">
                <el-input placeholder="商品条形码" class="table-input" :disabled="isEdit"
                            v-model="barCode"
                            :title="barCode"
                ></el-input>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { Message } from 'element-ui'
  import uploader from '@/utils/upload'
  import colorDialog from './color-dialog'

  export default {
    data() {
      // hasStandardSpec 是否有标准尺码属性，boolean
      // productSku 商品sku信息  从商品详情接口获得，新建的情况下不存在，编辑的情况下存在
      // colorList 规格-颜色 选择的list
      // specSubList 尺码属性二级list
      // specList 尺码属性一级list
      // specSelfList 自定义尺码属性list

      return {
        uploading: false,
        colorDialogVisible: false,
        hasStandardSpec: false,
        currency: 'EUR',
        productSku: [],
        colorList: [],
        specList: [],
        specSubList: [],
        specSelfList: [],
        colorIndex: 0,
        specRadio: '',
        specCheckList: [],
        goodsSpecCodeId: 0,
        watchFlag: false,
        barCode: '',
        officialPrice: '',
        stock: '',
        price: '',
        salePrice: '',
        rate: 1,
        fillPrice: '',
        fillStock: '',
        fillSkuCode: '',
        fillBarCode: '',
        lastColor: '',
        lastSelfSpec: '',
        allSku: false,
      }
    },
    props: {
      // 默认的所有属性规格的原始值，如果在编辑页，里面的原始值需要被goodsDetail里面对应的值覆盖
      quoteInfo: {
        type: Object,
        'default': {},
      },

      // 商品详情，在编辑页才会有，新建页只是一个默认的空对象，编辑页需要与quoteInfo配合
      goodsDetail: {
        type: Object,
        'default': {},
      },
      isEdit: {
        type: Boolean,
        'default': false,
      },
      productDetail: {
        'default': {},
        type: Object,
      },
      changeType: {
        'default': false,
      },
    },
    components: {
      colorDialog,
    },
    computed: {
      currencyInfo() {
        // 格式化货币符号
        if (this.quoteInfo.currency_info && this.quoteInfo.currency_info.length > 0) {
          const currency = this.quoteInfo.currency_info.filter(item => item.currency === this.currency)

          return currency[0]
        }

        return ''
      },
      rowspan() {
        return this.specSubList.filter(item => item.checked === true).length + this.specSelfList.length - 1
      },
      skuSpec() {
        const spec = []
        const specLen = this.allSpec.length

        this.productSku.forEach(item => {
          spec.push({
            sku_spec: item.sku_spec,
            // self: item.self,
          })
        })

        if (this.productSku.length === 0) {
          this.allSpec.forEach(item => {
            spec.push({
              sku_spec: item,
            })
          })
        }

        return spec.splice(0, specLen)
      },
      skuColor() {
        const color = []

        this.productSku.forEach(item => {
          color.push(item.sku_color)
        })

        return [...new Set(color)]
      },
      allSpec() {
        const allspec = []

        this.specSubList.filter(item => item.checked === true).forEach(item => {
          allspec.push(item.name)
        })
        this.specSelfList.forEach(item => {
          allspec.push(item.sku_spec)
        })

        allspec.pop()

        return allspec
      },
      allColor() {
        const allcolor = []

        this.colorList.forEach((item, index) => {
          if (index < this.colorList.length - 1) {
            allcolor.push(item.name)
          }
        })

        return allcolor
      },
      listenSpecSubList() {
        return this.specSubList.filter(item => item.checked === true)
      },
      listenSpecSelfList() {
        const arr = [...this.specSelfList]

        arr.pop()

        return arr
      },
      listenColorList() {
        return [...this.colorList]
      },
    },
    mounted() {
      // 如果是新建页面只需要初始化quote信息，quote信息见network中的getQuoteInfoByCate请求
      // 否则还需要初始化详情信息去覆盖一些默认的值
      this.initQuote()
      if (this.isEdit) {
        this.initDetail()
      }
      // eslint-disable-next-line
      if (!!this.changeType) {
        this.initChangeType()
      }
      this.initUpload()
    },
    methods: {
      initQuote() {
        if (!this.isEdit) {
          // 新建页下面的默认给颜色和自定义尺码输入框添加一个模版对象成员
          this.colorList.push({
            name: '',
            sku_image_list: [],
            checked: false,
          })
          this.specSelfList.push({
            checked: false,
            sku_spec: '',
          })
        }

        // 尺码默认展示首个，如果是编辑页，则默认展示可能被覆盖
        // this.quoteInfo.sku_info.data[1]不为空就代表该商品有系统提供的默认规格(没有默认规格的就需要自定义规格)
        if (this.quoteInfo.sku_info.data[1]) {
          this.specList = this.quoteInfo.sku_info.data[1].data_list
          this.specRadio = this.specList[0].code_name
          this.goodsSpecCodeId = this.specList[0].code_id || 0
          this.specSubList = this.specList[0].data_list
          this.hasStandardSpec = true
          this.specSubList.forEach(item => {
            // eslint-disable-next-line
            this.$set(item, 'checked' ,false)
          })
        }
        const currencyInfo = this.quoteInfo.currency_info
        const index = currencyInfo.findIndex(item => item.currency === this.currency)

        this.rate = this.quoteInfo.currency_info[index].rate
      },
      initDetail() {
        // colorList为用户创建的颜色列表，skuInfo为已经存在的sku信息，
        // 根据sku信息中的颜色判断colorList中有哪些颜色是被默认选中的
        // 规格（尺码）同理
        const colorList = [...this.goodsDetail.goods_color_list]
        const specList = [...this.goodsDetail.goods_size_list]
        const skuInfo = this.goodsDetail.product_sku
        const defaultSku = []

        this.productSku = this.goodsDetail.product_sku
        this.officialPrice = Number(this.goodsDetail.official_price) / 100
        this.stock = this.goodsDetail.stock
        this.price = this.goodsDetail.price / 100
        this.barCode = this.goodsDetail.ofashion_bar_code
        this.currency = this.goodsDetail.currency
        this.quoteInfo.currency_info.forEach(item => {
          if (item.currency === this.currency) {
            this.rate = item.rate
          }
        })

        colorList.forEach(item => {
          specList.forEach(subItem => {
            defaultSku.push({
              checked: false,
              sku_color: item.name,
              sku_spec: subItem.name,
              sku_price: '',
              product_sku_price: '',
              sku_code: '',
              sku_stock: '',
              sku_image_list: [],
            })
          })
        })
        this.productSku.forEach((item, index) => {
          defaultSku.forEach((subItem, subIndex) => {
            if (item.sku_spec === subItem.sku_spec && item.sku_color === subItem.sku_color) {
              defaultSku[subIndex] = item
              this.$set(item, 'checked', true)
            }
          })
        })

        this.productSku = defaultSku
        this.productSku.forEach(item => {
          if (typeof item.product_sku_price === 'number') {
            // eslint-disable-next-line
            item.product_sku_price = item.product_sku_price / 100
          }
        })

        this.productSku.forEach(item => {
          if (typeof item.sku_price === 'number') {
            // eslint-disable-next-line
            item.sku_price = item.sku_price / 100
          }
        })

        // goods_spec_code_id不为0的情况下，根据它去找到默认选中的一级尺码和二级尺码
        const specCodeId = Number(this.goodsDetail.goods_spec_code_id)

        // this.goodsSpecCodeId = this.goodsDetail.goods_spec_code_id
        if (specCodeId > 0) {
          this.specList.forEach((item, index) => {
            if (specCodeId === item.code_id) {
              this.specRadio = item.code_name
              this.goodsSpecCodeId = item.code_id
              this.specSubList = this.quoteInfo.sku_info.data[1].data_list[index].data_list
            }
          })
        }

        this.specSubList.forEach(item => {
          this.$set(item, 'checked', false)
        })

        // 从skuInfo中遍历出默认的颜色和尺码用于默认加载显示
        const skuCopy = [...skuInfo]
        let colorArr = []
        let specArr = []
        const colorObjArr = []

        // 获取默认的颜色和颜色并去重
        skuCopy.forEach(item => {
          colorArr.push(item.sku_color)
          specArr.push(item.sku_spec)
        })

        colorArr = [...new Set(colorArr)]
        specArr = [...new Set(specArr)]

        colorArr.forEach((item, index) => {
          skuCopy.forEach((subItem, subIndex) => {
            if (item === subItem.sku_color) {
              colorObjArr[index] = subItem.sku_image_list
            }
          })
        })

        colorArr.forEach((item, index) => {
          this.colorList.push({
            name: item,
            sku_image_list: colorObjArr[index],
            checked: true,
          })
        })

        specArr.forEach((item, index) => {
          if (specCodeId > 0) {
            // 筛选出skuInfo跟specSubList相同规格名称，不相同的则为自定义规格
            this.specSubList.forEach((subItem, subIndex) => {
              if (item === subItem.name) {
                subItem.checked = true
                specArr[index] = ''
              }
            })
          }
        })
        specArr = specArr.filter(item => item !== '')

        // 获取默认的自定义规格
        specArr.forEach((item, index) => {
          this.specSelfList.push({
            checked: true,
            // self: true,
            sku_spec: item,
          })
        })

        this.colorList.push({
          name: '',
          sku_image_list: [],
          checked: false,
        })
        this.specSelfList.push({
          checked: false,
          sku_spec: '',
        })
      },
      initUpload() {
        const uploadSetting = {
          browse_button: `upload-color-trigger`,
          container: `upload-color-area`,
          postfiles_button: `upload-color-files`,
          path_name: 'goods',
          max_file_size: '3M',
          filesAdded: () => {
            this.$message({
              showClose: true,
              message: '上传中',
              type: 'warning',
              onClose: () => {
                uploader.cancelUpload()
              },
            })
          },

          // 上传成功回调
          success: data => {
            this.$message.success('上传成功')
            this.$set(this.colorList[this.colorIndex], 'sku_image_list', [data])
            this.productSku.forEach(item => {
              if (item.sku_color === this.colorList[this.colorIndex].name) {
                this.$set(item, 'sku_image_list', [data])
              }
            })
          },

          // 上传失败回调：包括前后端对上传的文件验证不通过、前后端网络异常
          error: msg => {
            this.$message.error(msg)
          },
        }

        uploader.init(uploadSetting)
      },

      // 新建页面修改类目后返回继续编辑，将缓存的数据渲染，减少用户重复操作
      initChangeType() {
        const productSku = this.productDetail.productSku
        const specs = []

        this.colorList = this.productDetail.colorList
        this.specSelfList = this.productDetail.specSelfList
        this.officialPrice = this.productDetail.official_price
        this.barCode = this.productDetail.ofashion_bar_code

        // 如果存在默认的标准规格,则进行规格匹配
        if (this.quoteInfo.sku_info.data[1] && this.quoteInfo.sku_info.data[1].data_list.length > 0) {
          this.specList = this.quoteInfo.sku_info.data[1].data_list
          this.specList.forEach(item => {
            // 如果缓存中选中的标准规格和现有规格有匹配
            if (this.productDetail.specRadio === item.code_name) {
              this.specRadio = item.code_name
              this.goodsSpecCodeId = item.code_id
              this.specSubList = this.productDetail.specSubList
              this.productSku = productSku
            }
          })
          if (!this.specList.some(item => item.code_name === this.productDetail.specRadio)) {
            const specSubList = this.productDetail.specSubList.filter(item => item.checked === true)
            const specSelfList = this.productDetail.specSelfList

            specSelfList.pop()
            specSubList.forEach(item => {
              // specs.push(item.name)
              specSelfList.push({
                checked: true,
                sku_spec: item.name,
              })
            })
            specSelfList.forEach(item => {
              specs.push(item.sku_spec)
            })
            specSelfList.push({
              checked: false,
              sku_spec: '',
            })

            this.specSubList.forEach(item => {
              item.checked = false
              if (specs.includes(item.name)) {
                item.checked = true
                specSelfList.forEach((subItem, subIndex) => {
                  if (item.name === subItem.sku_spec) {
                    specSelfList.splice(subIndex, 1)
                  }
                })
              }
            })
            this.specSelfList = specSelfList
            this.productSku = productSku.filter(item => specs.indexOf(item.sku_spec) >= 0)
          }
        } else {
          const specSubList = this.productDetail.specSubList.filter(item => item.checked === true)
          const specSelfList = this.productDetail.specSelfList

          specSelfList.pop()
          specSubList.forEach(item => {
            // specs.push(item.name)
            specSelfList.push({
              checked: true,
              sku_spec: item.name,
            })
          })
          specSelfList.push({
            checked: false,
            sku_spec: '',
          })

          this.specSubList.forEach(item => {
            item.checked = false
          })
          this.specSelfList = specSelfList
          this.productSku = productSku
        }
      },
      isPositiveInt(e, target, prop = '') {
        const r = /^\+?[1-9][0-9]*$/

        if (!r.test(e)) {
          if (typeof target === 'string') {
            this[target] = ''
          }
          if (typeof target === 'object') {
            target[prop] = ''
          }
          this.$message.error('请输入正整数')
        }
      },
      isInt(e, target, prop = '') {
        const r = /^[1-9]\d*|0$/

        if (!r.test(e)) {
          if (typeof target === 'string') {
            this[target] = ''
          }
          if (typeof target === 'object') {
            target[prop] = ''
          }
          this.$message.error('请输入整数')
        }
      },
      fill() {
        this.productSku.forEach(item => {
          if (this.fillStock !== '') {
            item.sku_stock = this.fillStock
          }
          if (this.fillPrice !== '') {
            item.sku_price = this.fillPrice
          }
          if (this.fillSkuCode !== '') {
            item.sku_code = this.fillSkuCode
          }
          if (this.fillBarCode !== '') {
            this.barCode = this.fillBarCode
          }
        })
      },
      uploadImg(e, index) {
        this.colorIndex = index
        document.querySelector('#upload-color-trigger').click()
      },
      deleteImg(e, index) {
        this.colorIndex = index
        this.$set(this.colorList[this.colorIndex], 'sku_image_list', [])
        this.productSku.forEach(item => {
          if (item.sku_color === this.colorList[this.colorIndex].name) {
            this.$set(item, 'sku_image_list', [])
          }
        })
      },
      selectAllSku(e) {
        if (e) {
          this.productSku.forEach(item => {
            item.checked = true
          })
        } else {
          this.productSku.forEach(item => {
            item.checked = false
          })
        }
      },
      selectSku(e, spec) {
        if (e) {
          this.specSelfList.forEach(item => {
            if (item.sku_spec === spec &&
              this.productSku.filter(subItem => subItem.sku_spec === spec).every(self => self.checked === true)) {
              item.checked = true
            }
          })
        } else {
          this.specSelfList.forEach(item => {
            if (item.sku_spec === spec) {
              item.checked = false
            }
          })
        }
      },
      selectSelfSpec(e, spec) {
        if (e) {
          this.productSku.forEach(item => {
            if (item.sku_spec === spec) {
              item.checked = true
            }
          })
        } else {
          this.productSku.forEach(item => {
            if (item.sku_spec === spec) {
              item.checked = false
            }
          })
        }
      },
      openColorDialog(e, index, color) {
        this.colorIndex = index
        this.lastColor = color
        this.colorDialogVisible = true
        this.$nextTick(() => {
          const colorDialog = this.$refs.colorDialog.$el
          const srcEle = e.target.offsetParent

          srcEle.appendChild(colorDialog)
        })
      },
      closeColorDialog(colorName) {
        if (colorName !== undefined) {
          this.colorList[this.colorIndex].name = colorName
          this.colorChange(colorName, this.colorIndex)
        }
        this.$nextTick(() => {
          this.colorDialogVisible = false
        })
      },
      setLastSelfSpec(spec) {
        this.lastSelfSpec = spec
      },
      changeCurrency() {
        const oldRate = this.rate

        this.quoteInfo.currency_info.forEach(item => {
          if (item.currency === this.currency) {
            this.currency = item.currency
            this.rate = item.rate
          }
        })
        // this.officialPrice = this.officialPrice === '' ? '' : (this.officialPrice / (this.rate / oldRate)).toFixed(2)
      },
      colorChange(e, colorIndex) {
        // 如果编辑的是最后一个颜色输入框
        if (colorIndex === this.colorList.length - 1) {
          // 判断颜色是否重复
          // eslint-disable-next-line
          const repeat = this.colorList.some((item, index) => {
            if (index !== this.colorList.length - 1) {
              return item.name === e
            }
          })

          // 如果重复就清空
          if (repeat) {
            this.colorList[this.colorList.length - 1].name = ''
            this.$alert('存在重复的规格', '提示', {
              confirmButtonText: '确定',
              callback: action => {
                // ...
              },
            })
          } else {
            // 否则自动添加一个颜色自定义框
            this.colorList.push({
              name: '',
              sku_image_list: [],
              checked: false,
            })
          }
        } else {
          // 如果编辑的不是最后一个颜色框，不用考虑添加自定义颜色框的情况
          const repeat = this.colorList.some((item, index) => {
            if (index !== this.colorIndex) {
              return item.name === e
            }
          })

          if (repeat) {
            this.colorList[colorIndex].name = this.lastColor
            this.$alert('存在重复的规格', '提示', {
              confirmButtonText: '确定',
              callback: action => {
                // ...
              },
            })
          } else {
            this.productSku.forEach((item, index) => {
              if (item.sku_color === this.lastColor) {
                item.sku_color = e
              }
            })
          }
        }

        this.$nextTick(() => {
          this.colorDialogVisible = false
        })
      },
      selfChange(e, selfIndex) {
        if (selfIndex === this.specSelfList.length - 1) {
          // 判断自定义尺码是否重复
          // 1.判断是否和自定义尺码重复
          // eslint-disable-next-line
          const repeatSelf = this.specSelfList.some((item, index) => {
            if (index !== this.specSelfList.length - 1) {
              return item.sku_spec === e
            }
          })

          // 2.判断是否和标准尺码重复
          const repeatSub = this.specSubList.some((item, index) => item.name === e)

          // 如果重复就清空
          if (repeatSelf && !repeatSub) {
            this.specSelfList[this.specSelfList.length - 1].sku_spec = ''
            this.$alert('存在重复的规格', '提示', {
              confirmButtonText: '确定',
              callback: action => {
                // ...
              },
            })
          } else if (!repeatSelf && !repeatSub) {
            // 否则自动添加一个尺码自定义框
            this.specSelfList.push({
              sku_spec: '',
              // self: true,
              checked: false,
            })
          }

          if (repeatSub && !repeatSelf) {
            // 清空重复的自定义属性，选中标准规格中的属性
            this.specSelfList[this.specSelfList.length - 1].sku_spec = ''
            const subSpec = this.specSubList.find(item => item.name === e)

            subSpec.checked = true
            this.$alert('与标准规格中存在的规格重复，已帮您自动选中', '提示', {
              confirmButtonText: '确定',
              callback: action => {

              },
            })
          }
        } else {
          // 编辑的不是最后一个框
          const repeatSelf = this.specSelfList.some((item, index) => {
            if (index !== selfIndex) {
              return item.sku_spec === e
            }
          })

          // 2.判断是否和标准尺码重复
          const repeatSub = this.specSubList.some((item, index) => item.name === e)

          if (repeatSelf && !repeatSub) {
            this.specSelfList[selfIndex].sku_spec = this.lastSelfSpec
            this.$alert('存在重复的规格', '提示', {
              confirmButtonText: '确定',
              callback: action => {
                // ...
              },
            })
          } else if (!repeatSelf && !repeatSub) {
            this.productSku.forEach((item, index) => {
              if (item.sku_spec === this.lastSelfSpec) {
                item.sku_spec = e
              }
            })
          }

          if (!repeatSelf && repeatSub) {
            const subSpec = this.specSubList.find(item => item.name === e)

            subSpec.checked = true
            this.$alert('与标准规格中存在的规格重复，已帮您自动选中', '提示', {
              confirmButtonText: '确定',
              callback: action => {
                this.specSelfList[selfIndex].sku_spec = this.lastSelfSpec
                this.specSelfList.splice(selfIndex, 1)
              },
            })
          }
        }
      },
      deleteInput(index, list) {
        if (index === list.length - 1) return
        list.splice(index, 1)
      },
      specChange(e) {
        this.specList.forEach(item => {
          if (item.code_name === e) {
            this.specSubList = item.data_list
            this.goodsSpecCodeId = item.code_id
          }
        })
        this.specSubList.forEach(item => {
          this.$set(item, 'checked', false)
        })

        // 切换尺码后判断是否跟现有的自定义尺码有重复
        const subList = this.specSubList.map(item => item.name)
        const selfList = this.specSelfList.map(item => item.sku_spec)
        let repeat = false

        selfList.pop()

        subList.forEach((item, index) => {
          if (selfList.indexOf(item) > -1) {
            repeat = true
            const selfIndex = this.specSelfList.findIndex(selfItem => selfItem.sku_spec === item)
            const subIndex = this.specSubList.findIndex(subItem => subItem.name === item)

            this.specSelfList.splice(selfIndex, 1)
            this.$nextTick(() => {
              this.specSubList[subIndex].checked = true
            })
          }
        })
        if (repeat) {
          this.$alert('与标准规格中存在的规格重复，已帮您自动选中', '提示', {
            confirmButtonText: '确定',
            callback: action => {
              // ...
            },
          })
        }
      },
    },
    updated() {
      this.watchFlag = true
    },
    watch: {
      listenColorList: {
        handler(val, oldVal) {
          if (this.watchFlag) {
            // 增加一个颜色
            if (val.length > oldVal.length) {
              const diff = val[val.length - 2].name

              this.skuSpec.forEach(item => {
                this.productSku.push({
                  checked: false,
                  sku_color: diff,
                  sku_spec: item.sku_spec,
                  sku_price: '',
                  product_sku_price: '',
                  sku_code: '',
                  sku_stock: '',
                  sku_image_list: [],
                })
              })
            }

            // 删除一个颜色
            if (val.length < oldVal.length) {
              let diff = ''
              let diffIndex = 0

              for (let i = 0; i < val.length; i++) {
                if (val[i].name !== oldVal[i].name) {
                  diff = oldVal[i].name
                  diffIndex = i
                  break
                }
              }
              if (diff === '') {
                diff = oldVal[oldVal.length - 1].name
                diffIndex = oldVal.length - 1
              }

              this.productSku = this.productSku.filter(item => item.sku_color !== diff)
            }
          }
        },
        deep: true,
      },
      listenSpecSelfList: {
        handler(val, oldVal) {
          if (this.watchFlag) {
            // 增加一个自定义尺码
            if (val.length > oldVal.length) {
              const diff = val[val.length - 1].sku_spec
              const spenLen = this.skuSpec.length

              this.colorList.forEach((item, index) => {
                if (index !== this.colorList.length - 1) {
                  this.productSku.splice(spenLen * (index + 1) - 1, 0, {
                    checked: false,
                    sku_color: item.name,
                    sku_spec: diff,
                    sku_price: '',
                    product_sku_price: '',
                    sku_code: '',
                    sku_stock: '',
                    sku_image_list: item.sku_image_list,
                  })
                }
              })
            }

            // 删除一个自定义尺码
            if (val.length < oldVal.length) {
              let diff = ''

              for (let i = 0; i < val.length; i++) {
                if (val[i].sku_spec !== oldVal[i].sku_spec) {
                  diff = oldVal[i].sku_spec
                  break
                }
              }

              if (diff === '') {
                diff = oldVal[oldVal.length - 1].sku_spec
              }

              this.productSku = this.productSku.filter(item => item.sku_spec !== diff)
            }
          }
        },
        deep: true,
      },
      listenSpecSubList: {
        handler(val, oldVal) {
          if (this.watchFlag) {
            // 选中一个标准规格
            if (val.length > oldVal.length) {
              let diff = ''
              const spenLen = this.skuSpec.length

              for (let i = 0; i < oldVal.length; i++) {
                if (oldVal[i].name !== val[i].name) {
                  diff = val[i].name
                  break
                }
              }
              if (diff === '') {
                diff = val[val.length - 1].name
              }

              this.colorList.forEach((item, index) => {
                if (index !== this.colorList.length - 1) {
                  this.productSku.splice(spenLen * (index + 1) - 1, 0, {
                    checked: false,
                    sku_color: item.name,
                    sku_spec: diff,
                    sku_price: '',
                    product_sku_price: '',
                    sku_code: '',
                    sku_stock: '',
                    sku_image_list: item.sku_image_list,
                  })
                }
              })
            }

            // 取消一个/多个标准规格
            if (val.length < oldVal.length) {
              const diff = []
              const valArr = []

              val.forEach(item => {
                valArr.push(item.name)
              })

              oldVal.forEach(item => {
                if (valArr.indexOf(item.name) < 0) {
                  diff.push(item.name)
                }
              })
              this.productSku = this.productSku.filter(item => diff.indexOf(item.sku_spec) < 0)
            }
          }
        },
        deep: true,
      },
      productSku: {
        handler(val) {
          const priceList = []
          let stockSum = 0
          const salePrice = []
          const filterSku = val.filter(item => {
            const emptyPrice = item.product_sku_price === '' || Number(item.product_sku_price) === 0
            const emptyStock = item.sku_stock === ''

            return !emptyStock && !emptyPrice
          })

          // this.allSku = val.every(item => item.checked === true)
          filterSku.forEach(item => {
            if (typeof Number(item.sku_price) === 'number' && Number(item.sku_price) !== 0) {
              salePrice.push(Number(item.sku_price))
            }
            if (typeof Number(item.product_sku_price) === 'number' && Number(item.product_sku_price) !== 0) {
              priceList.push(Number(item.product_sku_price))
            }
            stockSum += Number(item.sku_stock)
          })

          const minPrice = priceList.length > 0 ? Math.min(...priceList) : 0
          const maxPrice = priceList.length > 0 ? Math.max(...priceList) : 0
          const minSalePrice = salePrice.length > 0 ? Math.min(...salePrice) : 0
          const maxSalePrice = salePrice.length > 0 ? Math.max(...salePrice) : 0

          if (minPrice === maxPrice) {
            this.price = priceList.length > 0 ? minPrice : 0
          } else {
            this.price = priceList.length > 0 ? `${minPrice} ~ ${maxPrice}` : 0
          }

          if (minSalePrice === maxSalePrice) {
            this.salePrice = salePrice.length > 0 ? minSalePrice : 0
          } else {
            this.salePrice = salePrice.length > 0 ? `${minSalePrice} ~ ${maxSalePrice}` : 0
          }

          this.stock = stockSum
        },
        deep: true,
      },
    },
  }
</script>

<style lang="scss" scoped>
  @import "goods-spec";
</style>
<style lang="scss">
  .table-row {
    .table-select{
      width:120px;
      .el-input{
        width:100%;
      }
    }
  }
  .speclist {
    .el-checkbox__label {
      display: none;
    }
  }
  .table-input{
    width: 100%;
    .el-input__inner {
      border:none;
      text-align:center;
    }
  }
</style>
