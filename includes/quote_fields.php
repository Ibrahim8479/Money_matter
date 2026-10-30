<?php
$YN = ['Yes','No','Not sure']; $BUD = ['Under GHS 1,000','GHS 1,000 - 3,000','GHS 3,000 - 7,000','GHS 7,000+','Not sure yet'];
return [
 'website-development' => [
  ['web_type','Type of website','select',['Business / company','Online shop','Portfolio','Landing page','Other']],
  ['web_pages','Approximate number of pages','text'],
  ['web_domain','Do you already have a domain?','select',$YN],['web_hosting','Do you already have hosting?','select',$YN],
  ['web_content','Do you have content ready?','select',$YN],['web_integrations','Required integrations (payments, maps, booking...)','text'],
  ['web_launch','Desired launch period','select',['ASAP','1 month','2-3 months','Flexible']],['web_budget','Budget range','select',$BUD]],
 'social-media-management' => [
  ['smm_platforms','Platforms (Instagram, Facebook, TikTok...)','text'],['smm_count','Number of platforms','text'],
  ['smm_frequency','Posting frequency','select',['2-3 / week','4-5 / week','Daily','Not sure']],
  ['smm_existing','Existing social pages?','select',$YN],['smm_content','Content creation needed?','select',$YN],
  ['smm_ads','Advertising support needed?','select',$YN],['smm_budget','Monthly budget range','select',$BUD],['smm_goals','Goals','text']],
 'website-maintenance' => [
  ['mnt_url','Existing website URL','text'],['mnt_cms','Platform / CMS (if known)','text'],
  ['mnt_type','Type of maintenance needed','select',['Content updates','Bug fixes','Security & backups','Everything']],
  ['mnt_frequency','Update frequency','select',['Weekly','Monthly','As needed']],['mnt_issues','Main issues','text'],['mnt_budget','Budget range','select',$BUD]],
];
