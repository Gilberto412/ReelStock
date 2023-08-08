import { clone } from "./utils.js";

export default {
	is,
	isEmpty,
	findByPropertyValue,
	findIndexByPropertyValue,
	insertByIndex,
	removeByIndex,
	removeByValue,
	cloneItem,
	changeItemPosition,
	moveItemToArray,
	cloneItemToArray,
	totalValue,
};

export function is(arr) {
	return Array.isArray(arr);
};

export function isEmpty(array) {
	return Array.isArray(array) && array.length > 0
		? false
		: true;
}

export function findByPropertyValue(arr, prop, val) {
	return arr.find(
		item => item.hasOwnProperty(prop) && item[prop] == val
	);
};

export function findIndexByPropertyValue(arr, prop, val) {
	if (!is(arr))
		return false;

	return arr.findIndex(
		item => item.hasOwnProperty(prop) && item[prop] == val
	);
};

export function insertByIndex(array, index, item, returnNew = false) {
	if (returnNew) {
		const outputArray = [...array];

		outputArray.splice(index, 0, item);

		return outputArray;
	} else {
		array.splice(index, 0, item);
	}
}

export function removeByIndex(array, index, returnNew = false) {
	if (returnNew) {
		const outputArray = [...array];

		outputArray.splice(index, 1);

		return outputArray;
	} else {
		array.splice(index, 1);
	}
}

export function removeByValue(array, val) {
	const index = array.indexOf(val);

	if (index > -1) {
		array.splice(index, 1);
	}

	return array;
}

export function cloneItem(array, index) {
	return clone(array[index]);
}

export function changeItemPosition(array, from, to, returnNew = false) {
	if (returnNew) {
		const outputArray = [...array];

		outputArray.splice(to, 0, outputArray.splice(from, 1)[0]);

		return outputArray;
	} else {
		array.splice(to, 0, array.splice(from, 1)[0]);
	}
}

export function moveItemToArray(fromArray, from, toArray, to) {
	toArray.splice(to, 0, fromArray.splice(from, 1)[0]);
}

export function cloneItemToArray(fromArray, from, toArray, to) {
	toArray.splice(to, 0, clone(fromArray[from]));
}

export function totalValue(array) {
	return array.reduce((accumulator, currValue) => accumulator + currValue);
}