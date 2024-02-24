import { UserConfig, searchForWorkspaceRoot } from 'vite'
import path from 'path'

/**
 * Export vite plugin as default
 */
export default () => ({

  name: 'ja/shipping',

  config: (config: UserConfig): UserConfig => {

    config.resolve = config.resolve || {}

    config.resolve.alias = {
      ...(config.resolve.alias || {}),
      '@ja/shipping': path.resolve(`${__dirname}/resources/js`),
    }

    return config
  }
})
