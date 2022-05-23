;
$.sceditor.command
	.set("magnet", {
		exec: function(caller){ this.insert("[magnet]", "[/magnet]", false); },
		txtExec: ["[magnet]", "[/magnet]"],
		tooltip: "Magnet Link"
	});
