module.exports = {
    default: {
        paths: ['tests/bdd/features/**/*.feature'],
        require: ['tests/bdd/step-definitions/**/*.js'],
        format: ['progress']
    }
};