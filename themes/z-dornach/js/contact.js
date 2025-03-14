jQuery(function () {
  jQuery("#contact-form-customer").on("submit", submit_contact);
});

var tradTab = {
  en: {
    unkown_error:
      "An unknown error occurred while processing your request. Your request could not be taken into account. Please try again later.",
    invalid_value: "Please fill out this field.",
  },
  fr: {
    unkown_error:
      "Une erreur inattendue est survenue pendant le traitement de votre requête. Votre requête n'a peut être pas été prise en compte. Merci de réessayer plus tard.",
    invalid_value: "Merci de renseigner ce champ.",
  },
  nl: {
    unkown_error:
      "An unknown error occurred while processing your request. Your request could not be taken into account. Please try again later.",
    invalid_value: "Please fill out this field.",
  },
  de: {
    unkown_error:
      "An unknown error occurred while processing your request. Your request could not be taken into account. Please try again later.",
    invalid_value: "Please fill out this field.",
  },
};

function trad(code) {
  var lang = window.navigator.language.slice(0, 2).toLowerCase();
  if (lang != "fr" && lang != "nl" && lang != "en" && lang != "de") {
    lang = "en";
  }

  return tradTab[lang][code];
}

function valid_contact_information() {
  var fields = [
      jQuery("#customer-fname-id"),
      jQuery("#customer-lname-id"),
      jQuery("#customer-email-id"),
      jQuery("#customer-object-id"),      
      jQuery("#customer-msg-id"),        
  ];

  const regEmpty = /^\s*$/;

  for (var i in fields) {
    fields[i][0].setCustomValidity("");

    var isEmptyString = false;
    if (
      fields[i][0].type != undefined &&
      (fields[i][0].type === "text" || fields[i][0].type === "textarea") &&
      fields[i][0].required != undefined &&
      fields[i][0].required === true
    ) {
      if (fields[i][0].value.match(regEmpty)) {
        isEmptyString = true;
      }
    }

    if (!fields[i][0].checkValidity()) {
      fields[i].attr("data-content", fields[i][0].validationMessage);
      fields[i].popover({ trigger: "focus" });
      fields[i].focus();
      return false;
    }

    if (isEmptyString === true) {
      fields[i][0].setCustomValidity(trad("invalid_value"));
      fields[i].focus();
      return false;
    }
  }

  return true;
}

function submit_contact(event) {
  event.preventDefault();
  event.stopPropagation();

  event.target.classList.add("was-validated");

  jQuery("#feedback-container").text("");
  jQuery("#feedback-container").removeClass("feedback-positive");
  jQuery("#feedback-container").removeClass("feedback-negative");

  if (!valid_contact_information()) {
    return;
  }

  jQuery(".was-validated").removeClass("was-validated");    

  if(typeof grecaptcha === 'undefined'){
    jQuery("#feedback-container").addClass("feedback-negative");
    jQuery("#feedback-container").removeClass("feedback-positive");
    jQuery("#feedback-container").text("Recaptcha is disabled by your cookies preferences. We use this service to ensure security of this form. Please enable Recaptcha in order to use this form.");
    return;
  }

  var form_data = new FormData();
  form_data.append("action", "contact_us");
  form_data.append("fn", jQuery("#customer-fname-id").val());
  form_data.append("ln", jQuery("#customer-lname-id").val());
  form_data.append("c", jQuery("#customer-comp-id").val());
  form_data.append("e", jQuery("#customer-email-id").val()); 
  form_data.append("o", jQuery("#customer-object-id").val()); 
  form_data.append("m", jQuery("#customer-msg-id").val());    

  var form = jQuery("#contact-form-customer");    

  form.find("input, textarea, select, button").attr("disabled", true);

  grecaptcha
    .execute(RECAPTCHA_SITE_KEY, {
      action: "dornach_contact",
    })
    .then(function (tk) {
      form_data.append("tk", tk);

      jQuery.ajax({
        url: AJAXURL,
        dataType: "json",
        method: "POST",
        contentType: false,
        processData: false,
        data: form_data,
        async: true,
        success: function (result) {
          jQuery("#feedback-container").removeClass("feedback-negative");
          jQuery("#feedback-container").addClass("feedback-positive");
          jQuery("#feedback-container").text(result.message);
          reset_contact_form();
        },
        error: function (xhr, statut, error) {          
          jQuery("#feedback-container").addClass("feedback-negative");
          jQuery("#feedback-container").removeClass("feedback-positive");
          if(xhr?.responseJSON?.message){
            jQuery("#feedback-container").text(xhr?.responseJSON?.message);
          }else{
            jQuery("#feedback-container").text(trad("unkown_error"));
          }
          
        },
      })
        .done(function () {
          form.find("input, textarea, select, button").attr("disabled", false);            
        })
        .fail(function (jqXHR, textStatus) {            
          form.find("input, textarea, select, button").attr("disabled", false);            
        });
    });
}

function reset_contact_form() {
  jQuery("#customer-fname-id").val("");    
  jQuery("#customer-lname-id").val("");     
  jQuery("#customer-comp-id").val("");     
  jQuery("#customer-email-id").val("");     
  jQuery("#customer-object-id").val("");     
  jQuery("#customer-msg-id").val("");    
}
