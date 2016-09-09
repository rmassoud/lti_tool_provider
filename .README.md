Manually create the database table as below


CREATE TABLE `lti_tool_provider_consumer` (
  `lti_tool_provider_consumer_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key of the lti_tool_provider_consumer.',
  `lti_tool_provider_consumer_key` varchar(512) NOT NULL COMMENT 'The key for LTITP Consumer',
  `lti_tool_provider_consumer_secret` varchar(512) NOT NULL COMMENT 'The secret for LTITP Consumer',
  `lti_tool_provider_consumer_consumer` varchar(512) NOT NULL COMMENT 'A representive name of LTITP Consumer',
  `lti_tool_provider_consumer_domain` varchar(512) NOT NULL COMMENT 'A representive name of the domain',
  `lti_tool_provider_consumer_dummy_pref` tinyint(4) NOT NULL COMMENT 'A representive name of the domain',
  `date_joined` int(11) NOT NULL COMMENT 'The Unix timestamp of the entity creation time.',
  PRIMARY KEY (`lti_tool_provider_consumer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The base table for our lti_tool_provider_consumer.';
