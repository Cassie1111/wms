const permissionTree = [
  {
    id: 1,
    name: '我的',
  },
  {
    id: 2,
    name: '供应商管理',
    children: [
      {
        id: 10,
        name: '我的供应商',
        children: [
          {
            id: 25,
            name: '查询',
          },
          {
            id: 26,
            name: '编辑',
          },
        ],
      },
    ],
  },
  {
    id: 3,
    name: '分销商管理',
    children: [
      {
        id: 11,
        name: '我的分销商',
        children: [
          {
            id: 27,
            name: '查询',
          },
          {
            id: 28,
            name: '编辑',
          },
        ],
      },
    ],
  },
  {
    id: 4,
    name: '货品管理',
    children: [
      {
        id: 12,
        name: '分销中的商品',
        children: [
          {
            id: 29,
            name: '查询',
          },
          {
            id: 30,
            name: '编辑',
          },
          {
            id: 31,
            name: '导入 / 导出商品',
          },
        ],
      },
      {
        id: 13,
        name: '我的仓库',
        children: [
          {
            id: 32,
            name: '查询',
          },
          {
            id: 33,
            name: '编辑',
          },
          {
            id: 34,
            name: '导入 / 导出商品',
          },
        ],
      },
      {
        id: 14,
        name: '创建新商品',
      },
    ],
  },
  {
    id: 5,
    name: '订单管理',
    children: [
      {
        id: 15,
        name: '我的订单',
        children: [
          {
            id: 35,
            name: '查询',
          },
          {
            id: 36,
            name: '发货管理',
          },
          {
            id: 37,
            name: '导入 / 导出',
          },
        ],
      },
      {
        id: 16,
        name: '我的采购单',
        children: [
          {
            id: 38,
            name: '查询',
          },
          {
            id: 39,
            name: '发货管理',
          },
          {
            id: 40,
            name: '导入 / 导出',
          },
        ],
      },
      {
        id: 17,
        name: '退款管理',
        children: [
          {
            id: 41,
            name: '编辑',
          },
        ],
      },
    ],
  },
  {
    id: 6,
    name: '营销中心',
    children: [
      {
        id: 18,
        name: '模版管理-轮播',
        children: [
          {
            id: 42,
            name: '查询',
          },
          {
            id: 43,
            name: '编辑',
          },
        ],
      },
      {
        id: 19,
        name: '模版管理-活动',
        children: [
          {
            id: 44,
            name: '查询',
          },
          {
            id: 45,
            name: '编辑',
          },
        ],
      },
      {
        id: 20,
        name: '活动管理',
        children: [
          {
            id: 46,
            name: '查询',
          },
          {
            id: 47,
            name: '编辑',
          },
        ],
      },
      {
        id: 21,
        name: '资源管理',
        children: [
          {
            id: 48,
            name: '查询',
          },
          {
            id: 49,
            name: '编辑',
          },
        ],
      },
      {
        id: 22,
        name: '推送',
        children: [
          {
            id: 50,
            name: '推送',
          },
          {
            id: 51,
            name: '短信',
          },
        ],
      },
    ],
  },
  {
    id: 7,
    name: '资金管理',
    children: [
      {
        id: 23,
        name: '结算单管理',
        children: [
          {
            id: 52,
            name: '编辑',
          },
        ],
      },
    ],
  },
  {
    id: 8,
    name: '设置',
    children: [
      {
        id: 24,
        name: '子账号管理',
        children: [
          {
            id: 53,
            name: '编辑',
          },
        ],
      },
    ],
  },
  {
    id: 9,
    name: '客服中心',
  },
]

export default permissionTree
