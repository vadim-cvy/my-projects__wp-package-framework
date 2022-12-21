module.exports.getBuildRootPath = isFramework => 'build/' + ( isFramework ? 'framework/' : '' );

module.exports.getSrcRootPath = isFramework => './' + ( isFramework ? 'framework/' : '' ) + 'view/';