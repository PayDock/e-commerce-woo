window.getValidationResults = function (phoneInputs, validatePhone) {
	return Object.entries( phoneInputs ).reduce(
		(acc, [key, input]) => {
			acc[key] = validatePhone( input );
			return acc;
		},
		{}
	);
};
