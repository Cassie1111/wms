<template>
  <!-- 属性,属性的标签是根据类目动态变化的-->
  <div class="table-row">
    <div class="row-label">属性</div>
    <div class="row-content row-content-bg property-content">
      <!--根据属性的data-type分为5类: 0.输入框___1.单选下拉框___2.单选下拉框加自定义___3.多选___4.二级属性-->
      <!-- 编辑页模版 -->
      <div class="table-row property-table" v-if="isEdit">
        <div class="flex half-width" v-for="(item,index) in property">
          <div class="row-label" :class="{'is-required': goodsDetail.product_property[index].is_required}">
            {{goodsDetail.product_property[index].property_name}}
          </div>
          <el-input v-model.trim="item.value_list[0].property_value"
                    size = "mini"
                    @change="change($event, index, 0)"
                    v-if="goodsDetail.product_property[index].data_type == 0"
                    :clearable="true"
                    placeholder="请填写"
          ></el-input>

          <el-select v-if="goodsDetail.product_property[index].data_type == 1"
                     v-model="item.value_list[0].property_value_id"
                     @change="change($event, index, 1)"
                     :clearable="true">
            <el-option
                v-for="option in goodsDetail.product_property[index].value_list"
                :key="option.property_value_id"
                :label="option.property_value"
                :value="option.property_value_id"
            >
            </el-option>
          </el-select>

          <el-select v-if="goodsDetail.product_property[index].data_type == 2"
                     :clearable="true"
                     filterable
                     allow-create
                     default-first-option
                     v-model="item.value_list[0].property_value" @change="change($event, index, 2)">
            <el-option
                v-for="option in goodsDetail.product_property[index].value_list"
                :key="option.property_value_id"
                :label="option.property_value"
                :value="option.property_value_id">
            </el-option>
          </el-select>

          <el-select v-if="goodsDetail.product_property[index].data_type == 3"
                     :clearable="true"
                     multiple
                     size="mini"
                     :collapse-tags="true"
                     placeholder="请选择(多选)"
                     v-model="item.tplVal" @change="change($event, index, 3)">
            <el-option
                v-for="option in goodsDetail.product_property[index].value_list"
                :key="option.property_value_id"
                :label="option.property_value"
                :value="option.property_value_id">
            </el-option>
          </el-select>

          <el-select v-model="item.value_list[0].property_value_id"
                     v-if="goodsDetail.product_property[index].data_type == 4"
                     @change="change($event, index, 4)"
                     :clearable="true"
          >
            <el-option-group
                v-if="group.sub_level.length > 0"
                v-for="group in goodsDetail.product_property[index].value_list"
                :key="group.property_value"
                :label="group.property_value">
              <el-option
                  v-for="item in group.sub_level"
                  :key="item.property_value_id"
                  :label="item.property_value"
                  :value="item.property_value_id">
              </el-option>
            </el-option-group>
            <el-option-group
                v-if="group.sub_level.length == 0"
                v-for="group in goodsDetail.product_property[index].value_list"
                :label="group.property_value">
              <el-option
                  :key="group.property_value_id"
                  :label="group.property_value"
                  :value="group.property_value_id">
              </el-option>
            </el-option-group>
          </el-select>
        </div>

      </div>

      <!--新建页模版-->
      <div class="table-row property-table" v-if="!isEdit">
        <div class="flex half-width" v-for="(item,index) in property">
          <div class="row-label" :class="{'is-required': propertyInfo[index].is_required}">{{item.property_name}}</div>
          <el-input v-model="item.value_list[0].property_value"
                    size = "mini"
                    v-if="propertyInfo[index].data_type == 0"
                    :clearable="true"
                    placeholder="请填写"
          ></el-input>

          <el-select v-if="propertyInfo[index].data_type == 1"
                     v-model="item.value_list[0].property_value_id"
                     @change="change($event, index, 1)"
                     :clearable="true"
          >
            <el-option
                v-for="option in propertyInfo[index].value_list"
                :key="option.property_value_id"
                :label="option.property_value"
                :value="option.property_value_id">
            </el-option>
          </el-select>

          <el-select v-if="propertyInfo[index].data_type == 2"
                     :clearable="true"
                     filterable
                     allow-create
                     default-first-option
                     v-model="item.value_list[0].property_value" @change="change($event, index, 2)">
            <el-option
                v-for="option in propertyInfo[index].value_list"
                :key="option.property_value_id"
                :label="option.property_value"
                :value="option.property_value_id">
            </el-option>
          </el-select>

          <el-select v-if="propertyInfo[index].data_type == 3"
                     :clearable="true"
                     multiple
                     placeholder="请选择(多选)"
                     :collapse-tags="true"
                     size="mini"
                     v-model="item.tplVal" @change="change($event, index, 3)">
            <el-option
                v-for="option in propertyInfo[index].value_list"
                :key="option.property_value_id"
                :label="option.property_value"
                :value="option.property_value_id">
            </el-option>
          </el-select>

          <el-select v-model="item.value_list[0].property_value_id"
                     v-if="propertyInfo[index].data_type == 4"
                     :clearable="true"
                     @change="change($event, index, 4)"
          >
            <el-option-group
                v-if="group.sub_level.length > 0"
                v-for="group in propertyInfo[index].value_list"
                :key="group.property_value"
                :label="group.property_value">
              <el-option
                  v-for="item in group.sub_level"
                  :key="item.property_value_id"
                  :label="item.property_value"
                  :value="item.property_value_id">
              </el-option>
            </el-option-group>
            <el-option-group
                v-if="group.sub_level.length == 0"
                v-for="group in propertyInfo[index].value_list"
                :label="group.property_value">
              <el-option
                  :key="group.property_value_id"
                  :label="group.property_value"
                  :value="group.property_value_id">
              </el-option>
            </el-option-group>
          </el-select>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
  export default {
    props: {
      propertyInfo: {
        'default': () => [],
        type: Array,
      },
      goodsDetail: {
        'default': {},
        type: Object,
      },
      isEdit: {
        'default': false,
        type: Boolean,
      },
      productDetail: {
        'default': {},
        type: Object,
      },
      changeType: {
        'default': false,
      },
    },
    data() {
      return {
        selectedValue: [],
        value: '',
        property: [],
      }
    },
    mounted() {
      this.initProperty()
      if (this.isEdit) {
        this.initGoodsDetail()
      }
      if (this.changeType) {
        this.initChangeType()
      }
    },
    methods: {
      change(e, index, dataType) {
        // eslint-disable-next-line
        const options = this.isEdit ? this.goodsDetail.product_property[index].value_list : this.propertyInfo[index].value_list

        if (dataType === 0) {
          options.forEach(item => {
            this.property[index].value_list[0].property_value = e
            this.property[index].value_list[0].property_value_id = item.property_value_id
          })
        }
        if (dataType === 1) {
          options.forEach(item => {
            if (e === item.property_value_id) {
              this.property[index].value_list[0].property_value = item.property_value
            }
          })
        }
        if (dataType === 2) {
          let selfProp = true

          options.forEach(item => {
            if (e === item.property_value_id) {
              this.property[index].value_list[0].property_value = item.property_value
              this.property[index].value_list[0].property_value_id = Number(e)
              selfProp = false
            }
          })

          if (selfProp) {
            this.property[index].value_list[0].property_value_id = Number(this.propertyInfo[index].custom_input_id)
            this.property[index].value_list[0].property_value = e
          }
        }
        if (dataType === 3) {
          const valueList = []

          options.forEach(item => {
            if (e.indexOf(item.property_value_id) > -1) {
              valueList.push({
                property_value: item.property_value,
                property_value_id: item.property_value_id,
              })
            }
          })

          this.$set(this.property[index], 'value_list', [
            {
              property_value: '',
              property_value_id: '',
            },
          ])
          this.$set(this.property[index], 'tplVal', e)
          valueList.forEach((item, subIndex) => {
            this.$set(this.property[index].value_list, subIndex, {
              property_value: item.property_value,
              property_value_id: item.property_value_id,
            })
          })
          console.log(e, valueList)
        }
        if (dataType === 4) {
          options.forEach(item => {
            console.log(item)
            item.sub_level.forEach(subItem => {
              if (e === subItem.property_value_id) {
                this.property[index].value_list[0].property_value = subItem.property_value
              }
            })
            if (item.sub_level.length === 0 && e === item.property_value_id) {
              this.property[index].value_list[0].property_value = item.property_value
            }
          })
        }

        if (e === '') {
          this.property[index].value_list.forEach(item => {
            item.property_value = ''
            item.property_value_id = ''
          })
        }
      },
      initGoodsDetail() {
        const property = [...this.goodsDetail.product_property]

        property.forEach((item, index) => {
          const valueList = item.selected_value_list[0]
          let propertyItem

          if (item.data_type !== 4 && item.data_type !== 3) {
            propertyItem = {
              property_id: item.property_id,
              value_list: [
                {
                  property_value_id: valueList ? valueList.property_value_id : '',
                  property_value: valueList ? valueList.property_value : '',
                },
              ],
            }
          }
          if (item.data_type === 3) {
            const valueList = []
            const tplArr = []

            item.selected_value_list.forEach(subItem => {
              valueList.push({
                property_value_id: subItem.property_value_id,
                property_value: subItem.property_value,
              })
              tplArr.push(subItem.property_value_id)
            })

            propertyItem = {
              tplVal: tplArr,
              property_id: item.property_id,
              property_name: item.property_name,
              value_list: valueList,
            }
          }
          if (item.data_type === 4) {
            if (valueList !== undefined) {
              if (valueList.sub_level.length > 0) {
                propertyItem = {
                  property_id: item.property_id,
                  value_list: [
                    {
                      property_value_id: valueList.sub_level[0].property_value_id || '',
                      property_value: valueList.sub_level[0].property_value || '',
                    },
                  ],
                }
              }
              if (valueList.sub_level.length === 0) {
                propertyItem = {
                  property_id: item.property_id,
                  value_list: [
                    {
                      property_value_id: valueList.property_value_id || '',
                      property_value: valueList.property_value || '',
                    },
                  ],
                }
              }
            } else {
              propertyItem = {
                property_id: item.property_id,
                value_list: [
                  {
                    property_value_id: '',
                    property_value: '',
                  },
                ],
              }
            }
          }
          this.property.push(propertyItem)
        })
      },
      initProperty() {
        if (!this.isEdit) {
          const property = [...this.propertyInfo]

          property.forEach((item, index) => {
            const valueList = item.value_list[0]
            let propertyItem

            if (item.data_type !== 3) {
              propertyItem = {
                property_id: item.property_id,
                property_name: item.property_name,
                value_list: [
                  {
                    property_value_id: valueList && item.data_type === 0 ? valueList.property_value_id : '',
                    property_value: '',
                  },
                ],
              }
            } else {
              propertyItem = {
                tplVal: '',
                property_id: item.property_id,
                property_name: item.property_name,
                value_list: [
                  {
                    property_value_id: '',
                    property_value: '',
                  },
                ],
              }
            }

            this.property.push(propertyItem)
          })
        }
      },
      initChangeType() {
        const cacheProperty = this.productDetail.product_property || []

        cacheProperty.forEach((item, index) => {
          this.property.forEach((subItem, subIndex) => {
            if (item.property_id === subItem.property_id) {
              this.$set(this.property[subIndex], 'property_id', item.property_id)
              this.$set(this.property[subIndex], 'value_list', item.value_list)
            }
          })
        })
      },
    },
  }
</script>

<style lang="scss" scoped>
  @import './goods-property';
</style>

<style lang="scss">
  .property-content {
    .row-label {
      flex: 0 0 116px;
    }
    .el-input__inner {
      font-size: 12px;
    }
  }
</style>

