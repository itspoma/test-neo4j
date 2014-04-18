* create/recreate the instance: `host$ vagrant destroy --force && vagrant up`
* re-runs the Chef: `host$ vagrant provision`
* enter to vagrant box: `host$ vagrant ssh`
* restart neo4j server: `vagrant$ sudo neo4j restart`
* updated web-panel: `http://33.33.33.33:7474/browser/`
* old web-panel: `http://33.33.33.33:7474/webadmin/`
* php demo: `http://33.33.33.33:80/`

---
**Restore backup:**

* `vagrant$ sudo mc -e /vagrant/neo4j/server/conf/neo4j.properties`
 * set `allow_store_upgrade=true`
* `vagrant$ sudo service neo4j stop`
* `vagrant$ sudo mc -e /vagrant/neo4j/server/conf/neo4j-server.properties`
* `vagrant$ mkdir -p /tmp/neo4j/db/ && cd /tmp/neo4j/db/`
* Datasets:
 * **DrWho (0.05MB):**
   * The Dr.Who universe of doctors, actors, enemies and props from the Neo4j Koans Tutorial.
   *  `mkdir /tmp/neo4j/db/drwho/`
   *  `wget -O /tmp/neo4j/db/drwho/dump.zip http://example-data.neo4j.org/files/drwho.zip`
   *  nodes=1'060; rels=2'286; props=1'075; rels types=16;
  * **Cineasts Movies & Actors (12.3MB):**
   * Full dataset (12k movies, 50k actors) of the Spring Data Neo4j Cineasts.net tutorial.
   * `mkdir /tmp/neo4j/db/cineasts_12k_movies_50k_actors/`
   * `wget -O /tmp/neo4j/db/cineasts_12k_movies_50k_actors/dump.zip http://example-data.neo4j.org/files/cineasts_12k_movies_50k_actors.zip`
   * (94 MB) => nodes=64'069; rels=121'778; props=426'800; rels types=5;
  * **Hubway Data Challenge (50MB):**
   * Hubway is a bike sharing service. The challenge data consists 95 Boston stations and 500k bike rides.
   * `mkdir /tmp/neo4j/db/hubway_data_challenge_boston/`
   * `wget -O /tmp/neo4j/db/hubway_data_challenge_boston/dump.zip http://example-data.neo4j.org/files/hubway_data_challenge_boston.zip`
   * (362 MB) => nodes=554'674; rels=2'011'904; props=1'660'489; rels types=10;
  * **The Musicbrainz main entities - large (5.4GB):**
   * Most of the interesting entities (800,000 Artists, 12,000,000 Tracks, 1,200,000 Releases, 75,000 Record Labels) from the Musicbrainz dataset
   * `mkdir /tmp/neo4j/db/musicbrainz_neo_20/`
   * `wget -O /tmp/neo4j/db/musicbrainz_neo_20/dump.zip http://example-data.neo4j.org/files/musicbrainz_neo_20.tar.bz2`
   * (362 MB) => nodes=?; rels=?; props=?; rels types=?;
* `cd /tmp/neo4j/db/{database}`
 * `cd /tmp/neo4j/db/drwho`
 * `cd /tmp/neo4j/db/cineasts_12k_movies_50k_actors`
 * `cd /tmp/neo4j/db/hubway_data_challenge_boston`
* `rm -rf data && mkdir data && mkdir data/graph.db`
* `unzip dump.zip -d data/graph.db/`
* `sudo chown neo4j:neo4j -R data/graph.db && ll data/`
* `sudo neo4j-shell -config /vagrant/neo4j/server/conf/neo4j.properties -path data/graph.db/ -v`
* `neo4j-shell$ start n=node(*) return count(n) as nodes;`
* `neo4j-shell$ start n=rel(*) return count(n) as relations;`
* `sudo rm -rf /usr/local/neo4j-server/data/log/neo4j* && sudo service neo4j restart`

---
**Examples:**
* `START me = node(0)`
* `START n=node(*) RETURN n LIMIT 10;`
* `START n=node(*) RETURN n.name, n.__type__, n LIMIT 10;`
* 
* cineasts_12k_movies_50k_actors
 * `START r=node(*) where r.name=~"(?i).*reeves.*" RETURN r.id as ID, r.name, r.__type__;`
 * `START r=node(*) where r.name=~".*Quentin.*" RETURN r.id as ID, r.name, r.__type__;`
 * `START r=node:Person(name="Quentin Tarantino") RETURN r;`
 * `START r=node:Person(name="Keanu Reeves") RETURN r.name;`
 * `START r=node(*) WHERE r.name = "Keanu Reeves" RETURN r;`
 * `START r=node:Person(name="Sasha Grey") RETURN r;`
 * `START r=rel(*) RETURN DISTINCT type(r) LIMIT 20;`
 * `START n=node(*) MATCH (n)-[r]->() RETURN DISTINCT type(r), count(r);`
 * `START keanu=node:Person(name="Keanu Reeves") MATCH (keanu)-[r]->() RETURN DISTINCT type(r);`
 * `START person=node:Person(*) MATCH (person)-[r]->() RETURN DISTINCT type(r);`
 * `START keanu=node:Person(name="Keanu Reeves") MATCH (keanu)-[r]->(friend) RETURN type(r), friend.__type__, type(r), friend.title LIMIT 10;`
 * `START keanu=node:Person(name="Keanu Reeves"), matrix=node:Movie(title="The Matrix Revisited") MATCH (keanu)-[r]->(matrix) RETURN type(r), matrix.__type__, type(r), matrix.title LIMIT 10;`
 * `START keanu=node:Person(name="Keanu Reeves"), matrix=node:Movie(title="The Matrix Revisited") MATCH (keanu)-[r1]->(matrix)<-[r2]-(friend) RETURN 'Keanu' as WHO, type(r1) as WHAT, matrix.title as `WHERE`, type(r2) as `FRIEND WHAT`, friend.name;`
 * `START keanu=node:Person(name="Keanu Reeves") MATCH (keanu)-[:ACTS_IN]->(film) RETURN film.title;`
* demo
 * `START n=node(*) RETURN n;`
 * `START n=node(*),m=node(*) WHERE (n)-[:FRIEND]->(m) RETURN n,m;`
 * `START n=node(*),m=node(*) WHERE (n)-[:LOVE]->(m) RETURN n,m;`
 * `MATCH (a)-[:FRIEND]->(b) RETURN a,b;`
 * `MATCH (a)-[:FRIEND]->()->[]->(b) WHERE a.name="Viktor" RETURN a,b;`
 * `START recruiter=node:Position(name="Recruiter") RETURN recruiter;`
 * `START recruiter=node:Position(name="Recruiter") MATCH (recruiter)<--(person) RETURN recruiter, person;`
 * `START recruiter=node:Position(name="Recruiter") MATCH (recruiter)<-[rel]-(person) RETURN recruiter, type(rel), person;`
 * `START recruiter=node:Position(name="Recruiter") MATCH (recruiter)<-[rel]-(person)-->(company) RETURN recruiter, type(rel), person,company;`
 * `START recruiter=node:Position(name="Recruiter") MATCH (recruiter)<-[:POSITION]-(person)-[:WORKS_IN]->(company) RETURN recruiter, person, company;`
 * `START recruiter=node:Position(name="Recruiter") MATCH (recruiter)<-[:POSITION]-(person:Person)-[:WORKS_IN]->(company:Company) RETURN recruiter, person, company;`
 * `START recruiter=node:Position(name="Recruiter")`
 * `MATCH (recruiter)<-[:POSITION]-(person:Person)-[:WORKS_IN]->(company:Company) RETURN person.name, company.name;`
 * `START u1 = node:Person(name="Alexei"), u2 = node:Person(name="Gennadiy") MATCH p = shortestPath( u1-[*]-u2 ) RETURN COLLECT(p)`
* create
 * `CREATE (key[:index] [:props]) (alex:Person {name:'Alexei'}), (anatoliy:Person {name:'Anatoliy'}), (benedikt:Person {name:'Benedikt'}), (epam:Company {name:'EPAM Systems'}), (php:Skill:Language {name:'php'}), (java:Skill:Language {name:'java'}), (andrew)-[:WORKS_FOR]->(epam), (evgen)-[:WORKS_FOR]->(epam), (petro)-[:HAS_SKILL]->(php) RETURN RETURN andrew;`