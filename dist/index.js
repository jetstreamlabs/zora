export const trans = (key, replace, Zora) => {
    let translation, translationNotFound = true

    try {
        translation = key.split('.').reduce((t, i) => t[i] || null, Zora.translations[window.locale].php)

        if (translation) {
            translationNotFound = false
        }
    } catch (e) {
        translation = key
    }

    if (translationNotFound) {
        translation = Zora.translations[window.locale]['json'][key]
            ? Zora.translations[window.locale]['json'][key]
            : key
    }

    __.forEach(replace, (value, key) => {
        translation = translation.replace(':' + key, value)
    })

    return translation
}
