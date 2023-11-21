const jqueryTransition = require('./jquery.transition');
window.$ = window.jQuery = require('jquery');

$(function() {
    PinSlider.init();
    SlotSlider.init();

    Accordion.init();
});

const PinSlider = {
	num_items: document.querySelectorAll(".pin").length,
	current: 1,
    width: 0,
	init: function() {
		this.addEvents();
	},
	addEvents: function() {
        const arrowLeft = $("#arrow_left");
        const arrowRight = $("#arrow_right");

        arrowLeft.addClass('disabled');

		arrowRight.on('click', () => {
            if (!arrowRight.hasClass('disabled')) {
                this.goToNext();
            }
            this.disable(arrowLeft, arrowRight);
		});

        arrowLeft.on('click', () => {
            if (!arrowLeft.hasClass('disabled')) {
			    this.goToPrevious();
            }
            this.disable(arrowLeft, arrowRight);
		});
	},

	goToNext: function() {
        this.width += $(".pin[data-position='" + this.current + "']").outerWidth(true);
		$(".pin-container").transition({ x: -this.width });
        this.current++;
        console.log(this.current);
	},

    goToPrevious: function() {
		this.width -= $(".pin[data-position='" + (this.current - 1) + "']").outerWidth(true);
		$(".pin-container").transition({ x: -this.width });
        this.current--;
        console.log(this.current);
	},

    disable: function(arrowLeft, arrowRight) {
        if (this.current == 1) {
           arrowLeft.addClass('disabled');
        } else if (this.current >= this.num_items) {
            arrowRight.addClass('disabled');
        }
        else {
            arrowRight.removeClass('disabled');
           arrowLeft.removeClass('disabled');
        }
    }
};

const SlotSlider = {
	num_items: document.querySelectorAll(".slot").length,
	current: 1,
    width: 0,
	init: function() {
		this.addEvents();
	},
	addEvents: function() {
        const arrowLeft = $(".support").find("#arrow_left");
        const arrowRight =$(".support").find("#arrow_right");
        arrowLeft.addClass('disabled');

		arrowRight.on('click', () => {
            if (!arrowRight.hasClass('disabled')) {
                this.goToNext();
            }
            this.disable(arrowLeft, arrowRight);
		});

        arrowLeft.on('click', () => {
            if (!arrowLeft.hasClass('disabled')) {
			    this.goToPrevious();
            }
            this.disable(arrowLeft, arrowRight);
		});
	},

	goToNext: function() {
        this.width += $(".slot[data-position='" + this.current + "']").outerWidth(true);
		$(".slot-container").transition({ x: -this.width });
        this.current++;
        console.log(this.current);
	},

    goToPrevious: function() {
		this.width -= $(".slot[data-position='" + (this.current - 1) + "']").outerWidth(true);
		$(".slot-container").transition({ x: -this.width });
        this.current--;
        console.log(this.current);
	},

    disable: function(arrowLeft, arrowRight) {
        if (this.current == 1) {
           arrowLeft.addClass('disabled');
        } else if (this.current >= this.num_items) {
            arrowRight.addClass('disabled');
        }
        else {
            arrowRight.removeClass('disabled');
           arrowLeft.removeClass('disabled');
        }
    }
};


const Accordion = {
    acc: document.getElementsByClassName("accordion"),

    init: function () {
        for (i = 0; i < this.acc.length; i++) {
            this.acc[i].addEventListener("click", function() {
                var div = $(this).next().next();
                div.toggle();
            });
        }
    }
};
