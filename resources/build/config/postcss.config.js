// postcss 配置文件

const cssnextOptions = {
  browserslist: [
    '> 1%',
    'last 2 versions',
    'not ie <= 8',
  ],
}

module.exports = ({ file, options, env }) => ({
  ident: 'postcss',
  parser: file.extname === '.sss' ? 'sugarss' : false,
  plugins: {
    'postcss-import': {
      root: file.dirname,
    },
    'postcss-cssnext': env === 'production' ? options.cssnext : false,
    autoprefixer: env === 'production' ? options.autoprefixer : false,
    cssnano: env === 'production' ? options.cssnano : false,
  },
})
