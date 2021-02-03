const path = require('path');

module.exports =  {

    entry: {
        app: ['./js/index.js', './js/appJ.js'],
    },
    output: {
        filename: './main.js',
        path: path.resolve(__dirname, './build')
    }

};