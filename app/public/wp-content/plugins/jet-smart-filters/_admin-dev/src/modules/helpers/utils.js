export default {
	getUniqueId,
	isNestingExist,
	getNesting,
	clone,
	debounce,
	stringToBoolean
};

export function getUniqueId() {
	return Math.random().toString(16).substr(2, 7);
}

export function isNestingExist(obj) {
	const nesting = Array.from(arguments).splice(1);
	let output = true;

	for (let key of nesting) {
		if (!obj[key]) {
			output = false;
			break;
		}

		obj = obj[key];
	}

	return output;
}

export function getNesting(obj) {
	const nesting = Array.from(arguments).splice(1);
	let isNestingExist = true;

	for (let key of nesting) {
		if (!obj[key]) {
			isNestingExist = false;
			break;
		}

		obj = obj[key];
	}

	return isNestingExist ? obj : false;
}

export function clone(o) {
	if (typeof o !== 'object')
		return o;

	let output, v, key;

	output = Array.isArray(o) ? [] : {};

	for (key in o) {
		v = o[key];
		output[key] = (typeof v === 'object') ? clone(v) : v;
	}

	return output;
}

export function debounce(callback, wait, immediate = false) {
	let timeout = null;

	return function () {
		const callNow = immediate && !timeout;
		const next = () => callback.apply(this, arguments);

		clearTimeout(timeout);
		timeout = setTimeout(next, wait);

		if (callNow) {
			next();
		}
	};
}

export function stringToBoolean(string) {
	if (typeof string === 'boolean')
		return string;

	switch (string.toLowerCase().trim()) {
		case 'true':
		case 'yes':
		case '1':
			return true;

		case 'false':
		case 'no':
		case '0':
		case null:
			return false;

		default:
			return Boolean(string);
	}
}