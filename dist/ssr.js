import _ from 'lodash'

export const trans = (key, replace, Zora) => {
	let translation,
		locale = process.env.LOCALE,
		translationNotFound = true

	try {
		translation = key.split('.').reduce((t, i) => t[i] || null, Zora.translations[locale].php)

		if (translation) {
			translationNotFound = false
		}
	} catch (e) {
		translation = key
	}

	if (translationNotFound) {
		try {
			translation = Zora.translations[locale]['json'][key]
		} catch (e) {
			translation = key
		}
	}

	_.forEach(replace, (value, key) => {
		translation = translation.replace(':' + key, value)
	})

	return translation
}
