<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    <xsl:output method="xml" indent="yes" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
                doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"/>

    <xsl:template match="/">

        <html xmlns="http://www.w3.org/1999/xhtml">

            <head>
                <meta charset="UTF-8"/>
                <link rel="stylesheet" href="dizajn.css"/>
                <title>Podaci</title>
            </head>

            <body>
                <header>
                    <a class="headerImageHomePageLink" href="index.html">
                        <img class="headerImage" src="resources/images/don_vito.png" alt="godfather_image"/>
                        <img class="headerImage" src="resources/images/homeBox.png" alt="house_icon"/>
                    </a>
                    <span class="emptyPadding"/>
                    <div class="pageTitle">Biografije Glumaca</div>
                </header>

                <section class="middleSection">
                    <nav>
                        <div class="navigationTitle">Poveznice</div>

                        <div class="navButtonsWrapper">
                            <input class="navButton" type="button" value="Početna Stranica"
                                   onclick="window.location.href='index.html';"/>

                            <input class="navButton" type="button" value="Pretraživanje"
                                   onclick="window.location.href='obrazac.html';"/>

                            <input class="navButton" type="button" value="Podaci"
                                   onclick="window.location.href='podaci.xml'"/>

                            <input class="navButton" type="button" value="Kolegij OR"
                                   onclick="window.location.href='http://www.fer.unizg.hr/predmet/or';"/>

                            <input class="navButton" type="button" value="Sjedište FER-a"
                                   onclick="window.open('http://www.fer.unizg.hr')"/>

                            <input class="navButton" type="button" value="E-mail autora"
                                   onclick="window.location.href='mailto:cindric95@gmail.com'"/>
                        </div>
                    </nav>


                    <div class="indexPageText">
                        <table id="glumciPodaci">
                            <tr>
                                <th>Ime</th>
                                <th>Prezime</th>
                                <th>Spol</th>
                                <th>Datum Rođenja</th>
                                <th>Nacionalnost</th>
                                <th>Broj Djece</th>
                                <th>Zanimanja</th>
                                <th>Filmografija</th>
                            </tr>

                            <xsl:for-each select="podaci/glumac">
                                <tr>
                                    <td>
                                        <xsl:value-of select="ime"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="prezime"/>
                                    </td>
                                    <td>
                                        <xsl:if test="@glumacSpol = 'M'">Muško</xsl:if>
                                        <xsl:if test="@glumacSpol = 'F'">Žensko</xsl:if>
                                    </td>
                                    <td>
                                        <xsl:value-of select="datum_rodjenja"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="@nacionalnost"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="@brojDjece"/>
                                    </td>
                                    <td>
                                        <xsl:for-each select="zanimanje">
                                            <xsl:value-of select="@imeZanimanja"/>
                                            <xsl:if test="position() != last()">,&#xA0;</xsl:if>
                                        </xsl:for-each>
                                    </td>
                                    <td>
                                        <xsl:value-of select="filmografija"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </div>
                </section>

                <footer>
                    Ivan Cindrić, 2019
                </footer>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>