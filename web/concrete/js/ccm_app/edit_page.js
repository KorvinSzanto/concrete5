/** 
 * concrete5 in context editing
 */

var CCMEditMode = function() {

	var blockTypeDropSuccessful = false;
	var $areaDropZones;
	var $sortableDropElement = false;
	var $draggableElement = false;

	setupMenus = function() {
		$('.ccm-area').ccmmenu();
		$('.ccm-block-edit').ccmmenu();
		$('.ccm-block-edit-layout').ccmmenu();

		$('.ccm-block-edit').each(function() {
			var $b = $(this);
			$b.find('a[data-menu-action=edit_inline]').on('click', function() {
				var bID = $b.attr('data-block-id');
				var aID = $b.attr('data-area-id');
				var arHandle = $b.closest('div.ccm-area').attr('data-area-handle');
				CCMInlineEditMode.editBlock(CCM_CID, aID, arHandle, bID, $(this).attr('data-menu-action-params'));
			});
		});		
	}

	saveArrangement = function(sourceBlockID, sourceBlockAreaID, destinationBlockAreaID) {
		var	cID = CCM_CID;
		jQuery.fn.dialog.showLoader();

		var serial = '&sourceBlockID=' + sourceBlockID + '&sourceBlockAreaID=' + sourceBlockAreaID + '&destinationBlockAreaID=' + destinationBlockAreaID
		var source = $('div.ccm-area[data-area-id=' + sourceBlockAreaID + ']');

		if (sourceBlockAreaID == destinationBlockAreaID) {
			var areaArray = [source];
		} else {
			var destination = $('div.ccm-area[data-area-id=' + destinationBlockAreaID + ']');
			var areaArray = [source, destination];
		}

		$.each(areaArray, function(idx, area) {
			var $area = $(area);
			areaStr = '&area[' + $area.attr('data-area-id') + '][]=';

			$area.find('> div.ccm-area-block-list > div.ccm-block-edit').each(function() {
				var bID = $(this).attr('data-block-id');
				if ($(this).attr('custom-style')) {
					bID += '-' + $(this).attr('custom-style');
				}
				serial += areaStr + bID;
			});
		});

	 	$.ajax({
	 		type: 'POST',
	 		url: CCM_DISPATCHER_FILENAME,
	 		dataType: 'json',
	 		data: 'cID=' + cID + '&ccm_token=' + CCM_SECURITY_TOKEN + '&btask=ajax_do_arrange' + serial,
	 		complete: function() {
		 		CCMEditMode.start();
		 	},
	 		success: function(r) {
	 			ccm_parseJSON(r, function() {
	 				jQuery.fn.dialog.hideLoader();
	 				if (source && destination) {
	 					// we are moving blocks from one area to another
						var stb = parseInt(source.attr('data-total-blocks'));
						var dtb = parseInt(destination.attr('data-total-blocks'));
						source.attr('data-total-blocks', stb - 1);
						destination.attr('data-total-blocks', dtb + 1);

						// we change the info on the block itself
						destination.find('div[data-block-id=' + sourceBlockID + ']').attr('data-area-id', destinationBlockAreaID);
						CCMToolbar.disableDirectExit();
					}
				});
	 		}
	 	});
	}


	addBlockType = function(cID, aID, arHandle, $link, fromdrag) {
		var btID = $link.attr('data-btID');
		var inline = parseInt($link.attr('data-supports-inline-editing'));
		var hasadd = parseInt($link.attr('data-has-add-template'));

		if (!hasadd) {
			var action = CCM_DISPATCHER_FILENAME + "?cID=" + cID + "&arHandle=" + encodeURIComponent(arHandle) + "&btID=" + btID + "&mode=edit&processBlock=1&add=1&ccm_token=" + CCM_SECURITY_TOKEN;
			$.get(action, function(r) { CCMEditMode.parseBlockResponse(r, false, 'add'); })
		} else if (inline) {
			CCMInlineEditMode.loadAdd(cID, arHandle, aID, btID);
		} else {
			jQuery.fn.dialog.open({
				onClose: function() {
					$(document).trigger('blockWindowClose');
					if (fromdrag) {
						jQuery.fn.dialog.closeAll();
						var ccm_blockTypeDropped = false;
					}
				},
				modal: false,
				width: parseInt($link.attr('data-dialog-width')),
				height: parseInt($link.attr('data-dialog-height')) + 20,
				title: $link.attr('data-dialog-title'),
				href: CCM_TOOLS_PATH + '/add_block_popup?cID=' + cID + '&btID=' + btID + '&arHandle=' + encodeURIComponent(arHandle)
			});
		}
	}

	setupSortablesAndDroppables = function() {
		
		// clean up in case we're running twice
		$('div.ccm-area-block-dropzone').remove();

		// empty areas are droppable. We have to 
		// declare them separately because sortable and droppable don't play as 
		// nicely together as they should.

		$emptyareas = $('div.ccm-area[data-total-blocks=0]');
		$emptyareas.droppable({
			hoverClass: 'ccm-area-drag-block-type-over',
			tolerance: 'pointer',
			accept: function($item) {
				var btHandle = $item.attr('data-block-type-handle');
				return $(this).attr('data-accepts-block-types').indexOf(btHandle) !== -1;
			},
			greedy: true,
			drop: function(e, ui) {
				$('.ccm-area-drag-block-type-over').removeClass('ccm-area-drag-block-type-over');
				if (ui.helper.is('.ccm-overlay-draggable-block-type')) {
					CCMEditMode.blockTypeDropSuccessful = true;
					// it's from the add block overlay
					addBlockType($(this).attr('data-cID'), $(this).attr('data-area-id'), $(this).attr('data-area-handle'), ui.helper, true);
				} else {
					// else we are dragging a block from some other area into this one.
					ui.draggable.appendTo($(this).find('.ccm-area-block-list'));
					saveArrangement(ui.draggable.attr('data-block-id'), ui.draggable.attr('data-area-id'), $(this).attr('data-area-id'));
				}
			}
		});

		var $dropzone = $('<div />').addClass('ccm-area-block-dropzone').append($('<div />').addClass('ccm-area-block-dropzone-inner'));
		$dropzone.clone().insertBefore($('.ccm-block-edit'));

		$nonemptyareas = $('div.ccm-area[data-total-blocks!=0] > div.ccm-area-block-list');
		$nonemptyareas.append($dropzone.clone());

		$('.ccm-area-block-dropzone').droppable({
			hoverClass: 'ccm-area-block-dropzone-over',
			tolerance: 'pointer',
			accept: function($item) {
				var btHandle = $item.attr('data-block-type-handle');
				var $area = $(this).closest('.ccm-area');
				var btHandles = $area.attr('data-accepts-block-types');
				if (btHandles) {
					return btHandles.indexOf(btHandle) !== -1;
				}
			},
			drop: function(e, ui) {
				$('.ccm-area-drag-block-type-over').removeClass('ccm-area-drag-block-type-over');
				if (ui.helper.is('.ccm-overlay-draggable-block-type')) {
					CCMEditMode.blockTypeDropSuccessful = true;
					$(this).replaceWith($('<div />', {'id': 'ccm-add-new-block-placeholder'}));
					// it's from the add block overlay
					var $area = $('#ccm-add-new-block-placeholder').closest('.ccm-area');
					addBlockType($area.attr('data-cID'), $area.attr('data-area-id'), $area.attr('data-area-handle'), ui.helper, true);
				} else {
					var bID = ui.draggable.attr('data-block-id');
					var arID = ui.draggable.attr('data-area-id');
					var $area = $(this).closest('.ccm-area');
					$(this).replaceWith(ui.draggable.clone());
					ui.draggable.remove();
					setTimeout(function() {
						saveArrangement(bID, arID, $area.attr('data-area-id'));
					}, 100); // i don't know why but we need to wait a moment so that the original draggable is out of the DOM
				}
			}

		});

		$('[data-inline-command=move-block]').on('mousedown', function() {
			$('.ccm-area-block-dropzone').addClass('ccm-area-block-dropzone-active');
		});

		$('.ccm-block-edit').draggable({
			cursor: 'move',
			cursorAt: {
				right: 10,
				top: 10
			},
			handle: '[data-inline-command=move-block]',
			opacity: 0.5,
			helper: function() {
				var w = $(this).width();
				var h = $(this).height();
				if (h > 300) {
					h = 300;
				}
				var $d =  $('<div />', {'class': 'ccm-block-type-sorting'}).css('width', w).css('height', h);
				$d.append($(this).clone());
				return $d;
			},
			stop: function() {
				$.fn.ccmmenu.enable();
			},
			start: function(e, ui) {
				// deactivate the menu on drag
				$.fn.ccmmenu.disable();
			}
		});		

	}

	return {
		start: function() {			
			setupMenus();
			setupSortablesAndDroppables();
		},

		setupBlockForm: function(form, bID, task) {
			form.ajaxForm({
				type: 'POST',
				iframe: true,
				beforeSubmit: function() {
					$('input[name=ccm-block-form-method]').val('AJAX');
					jQuery.fn.dialog.showLoader();
					if (typeof window.ccmValidateBlockForm == 'function') {
						r = window.ccmValidateBlockForm();
						if (ccm_isBlockError) {
							jQuery.fn.dialog.hideLoader();
							if(ccm_blockError) {
								ccmAlert.notice(ccmi18n.error, ccm_blockError + '</ul>');
							}
							ccm_resetBlockErrors();
							return false;
						}
					}
				},
				success: function(r) {
					CCMEditMode.parseBlockResponse(r, bID, task);
				}
			});
		},

		addBlockToScrapbook: function(cID, bID, arHandle) {
			CCMToolbar.disableDirectExit();
			// got to grab the message too, eventually
			$.ajax({
			type: 'POST',
			url: CCM_TOOLS_PATH + '/pile_manager.php',
			data: 'cID=' + cID + '&bID=' + bID + '&arHandle=' + arHandle + '&btask=add&scrapbookName=userScrapbook',
			success: function(resp) {
				ccmAlert.hud(ccmi18n.copyBlockToScrapbookMsg, 2000, 'add', ccmi18n.copyBlockToScrapbook);
			}});		
		},

		deleteBlock: function(cID, bID, aID, arHandle, msg) {
			if (confirm(msg)) {
				CCMToolbar.disableDirectExit();
				// got to grab the message too, eventually
				$d = $('[data-block-id=' + bID + '][data-area-id=' + aID + ']');
				$d.hide().remove();
				$.fn.ccmmenu.resethighlighter();
				ccmAlert.hud(ccmi18n.deleteBlockMsg, 2000, 'delete_small', ccmi18n.deleteBlock);
				var tb = parseInt($('[data-area-id=' + aID + ']').attr('data-total-blocks'));
				$('[data-area-id=' + aID + ']').attr('data-total-blocks', tb - 1);
				CCMEditMode.start();
				$.ajax({
					type: 'POST',
					url: CCM_DISPATCHER_FILENAME,
					data: 'cID=' + cID + '&ccm_token=' + CCM_SECURITY_TOKEN + '&isAjax=true&btask=remove&bID=' + bID + '&arHandle=' + arHandle
				});
				if (typeof window.ccm_parseBlockResponsePost == 'function') {
					ccm_parseBlockResponsePost({});
				}
			}	
		},

		parseBlockResponse: function(r, currentBlockID, task) {
			try { 
				r = r.replace(/(<([^>]+)>)/ig,""); // because some plugins add bogus HTML after our JSON requests and screw everything up
				resp = eval('(' + r + ')');
				if (resp.error == true) {
					var message = '<ul>'
					for (i = 0; i < resp.response.length; i++) {						
						message += '<li>' + resp.response[i] + '<\/li>';
					}
					message += '<\/ul>';
					ccmAlert.notice(ccmi18n.error, message);
				} else {
					jQuery.fn.dialog.closeTop();
					$(document).trigger('blockWindowAfterClose');
					if (resp.cID) {
						cID = resp.cID; 
					} else {
						cID = CCM_CID;
					}
					var action = CCM_TOOLS_PATH + '/edit_block_popup?cID=' + cID + '&bID=' + resp.bID + '&arHandle=' + encodeURIComponent(resp.arHandle) + '&btask=view_edit_mode';	 
					$.get(action, 		
						function(r) { 
							if (task == 'add') {
								if ($('#ccm-add-new-block-placeholder').length > 0) {
									$('#ccm-add-new-block-placeholder').before(r).remove();
									saveArrangement(resp.bID, resp.aID);
								} else {
									$("#a" + resp.aID + " > div.ccm-area-block-list").append(r);
								}
							} else {
								$('[data-block-id=' + currentBlockID + '][data-area-id=' + resp.aID + ']').before(r).remove();
							}
							CCMInlineEditMode.exit();
							CCMToolbar.disableDirectExit();
							jQuery.fn.dialog.hideLoader();
							if (task == 'add') {
								var tb = parseInt($('div.ccm-area[data-area-id=' + resp.aID + ']').attr('data-total-blocks'));
								$('div.ccm-area[data-area-id=' + resp.aID + ']').attr('data-total-blocks', tb + 1);
								ccmAlert.hud(ccmi18n.addBlockMsg, 2000, 'add', ccmi18n.addBlock);
								jQuery.fn.dialog.closeAll();
								CCMEditMode.start(); // refresh areas. 
							} else {
								ccmAlert.hud(ccmi18n.updateBlockMsg, 2000, 'success', ccmi18n.updateBlock);
							}
							if (typeof window.ccm_parseBlockResponsePost == 'function') {
								ccm_parseBlockResponsePost(resp);
							}
						}
					);
				}
			} catch(e) { 
				ccmAlert.notice(ccmi18n.error, r); 
			}
		},

		activateBlockTypesOverlay: function() {
			$('#ccm-dialog-block-types-sets ul a').on('click', function() {
				$('#ccm-overlay-block-types li').hide();
				$('#ccm-overlay-block-types li[data-block-type-sets~=' + $(this).attr('data-tab') + ']').show();
				$('#ccm-dialog-block-types-sets ul a').removeClass('active');
				$(this).addClass('active');
				return false;
			});

			$($('#ccm-dialog-block-types ul a').get(0)).trigger('click');

			$('#ccm-dialog-block-types').closest('.ui-dialog-content').addClass('ui-dialog-content-block-types');
			$('#ccm-block-type-search input').focus();
			if ($('#ccm-block-types-dragging').length == 0) {
				$('<div id="ccm-block-types-dragging" />').appendTo(document.body);
			}
			// remove any old add block type placeholders
			$('#ccm-add-new-block-placeholder').remove();
			$('#ccm-block-type-search input').liveUpdate('ccm-overlay-block-types');
			
			$('#ccm-block-type-search input').on('keyup', function() {
				if ($(this).val() == '') {
					$('#ccm-block-types-wrapper ul.nav-tabs').css('visibility', 'visible');
					$('#ccm-block-types-wrapper ul.nav-tabs li[class=active] a').click();
				} else {
					$('#ccm-block-types-wrapper ul.nav-tabs').css('visibility', 'hidden');
				}
			});

			$('#ccm-overlay-block-types a.ccm-overlay-draggable-block-type').each(function() {
				var $li = $(this);
				$li.css('cursor', 'move');
				$li.draggable({
					helper: 'clone',
					appendTo: $('#ccm-block-types-dragging'),
					revert: false,
					start: function(e, ui) {
						CCMEditMode.blockTypeDropSuccessful = false;
						$('.ccm-area-block-dropzone').addClass('ccm-area-block-dropzone-active');
						// handle the dialog
						$('#ccm-block-types-wrapper').parent().jqdialog('option', 'closeOnEscape', false);
						$('#ccm-overlay-block-types').closest('.ui-dialog').fadeOut(100);
						$('.ui-widget-overlay').remove();

						// deactivate the menu on drag
						$.fn.ccmmenu.disable();						

					},
					stop: function() {
						$.fn.ccmmenu.enable();
						if (!CCMEditMode.blockTypeDropSuccessful) {
							// this got cancelled without a receive.
							jQuery.fn.dialog.closeAll();
						}
					}
				});
			});

			$('a.ccm-overlay-clickable-block-type').on('click', function() {
				addBlockType($(this).attr('data-cID'), $(this).attr('data-area-id'), $(this).attr('data-area-handle'), $(this));
				return false;
			});
			
			
		}


	}

}();