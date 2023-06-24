export const trans = (key, replace, Zora) => {
  const locale = window.locale

  let translation = null

  try {
    translation = key
      .split('.')
      .reduce((t, i) => t[i] || null, Zora.translations[locale].php)

    if (translation) {
      return checkForVariables(translation, replace)
    }
  } catch (e) {}

  try {
    translation = Zora.translations[locale]['json'][key]

    if (translation) {
      return checkForVariables(translation, replace)
    }
  } catch (e) {}

  return checkForVariables(key, replace)
}

export const checkForVariables = (translation, replace) => {
  let translated = translation

  if (typeof replace === 'undefined') {
    return translation
  }

  replace.forEach((value, key) => {
    translated = translated.toString().replace(':' + key, value)
  })

  return translated
}
