

--- kept in main schema
CREATE SEQUENCE ukbms_year_index_values_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE ukbms_year_index_values
(
  id                      integer           NOT NULL DEFAULT nextval('ukbms_year_index_values_id_seq'::regclass),
  survey_id               integer           NOT NULL,
  year                    integer           NOT NULL,
  location_id             integer           NOT NULL,
  taxa_taxon_list_id      integer           NOT NULL,
  index                   integer           NOT NULL,
  deleted                 boolean           DEFAULT false NOT NULL,
  
  CONSTRAINT pk_ukbms_year_index_values PRIMARY KEY (id),
  CONSTRAINT fk_ukbms_year_index_value_survey FOREIGN KEY (survey_id) REFERENCES surveys(id),
  CONSTRAINT fk_ukbms_year_index_value_location FOREIGN KEY (location_id) REFERENCES locations(id),
  CONSTRAINT fk_ukbms_year_index_value_taxa_taxon_list FOREIGN KEY (taxa_taxon_list_id) REFERENCES taxa_taxon_lists(id)
)
WITH (
  OIDS=FALSE
);

COMMENT ON TABLE ukbms_year_index_values IS 'List of aggregated index values by year, location, taxon';

CREATE VIEW gv_ukbms_year_index_values AS
 SELECT s.title, w.title AS website, yiv.year, l.name, cttl.taxon, yiv.id
   FROM surveys s
   JOIN websites w ON s.website_id = w.id AND w.deleted = false
   JOIN ukbms_year_index_values yiv ON yiv.survey_id = s.id AND yiv.deleted = false
   JOIN locations l ON yiv.location_id = l.id AND l.deleted = false
   JOIN cache_taxa_taxon_lists cttl ON cttl.id=yiv.taxa_taxon_list_id
  WHERE s.deleted = false;

CREATE INDEX ix_ukbms_year_index_values_SLT ON ukbms_year_index_values USING btree (survey_id, location_id, taxa_taxon_list_id);

