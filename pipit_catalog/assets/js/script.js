window.addEventListener('load', function () {
	const fancySelect = new Choices('.pipit-filters__choices', {
		placeholder: true,
		placeholderValue: 'Type to search..',
		noChoicesText: 'No choices to choose from',
		itemSelectText: '',
	});
});