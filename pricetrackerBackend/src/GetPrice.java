import java.io.UnsupportedEncodingException;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.Statement;

import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;

import java.util.*;

import javax.mail.*;
import javax.mail.internet.*;
import javax.activation.*;

import java.util.Properties;

import javax.mail.Message;
import javax.mail.MessagingException;
import javax.mail.Session;
import javax.mail.Transport;
import javax.mail.internet.AddressException;
import javax.mail.internet.InternetAddress;
import javax.mail.internet.MimeMessage;

public class GetPrice {

	public static void main(String[] args) throws Exception {
		int webLinkCounter = 0;
		while (true) {
			Connection connect = null;
			Statement statement = null;
			PreparedStatement preparedStatement = null;
			try {
				// This will load the MySQL driver, each DB has its own driver
				Class.forName("com.mysql.jdbc.Driver");
				// Setup the connection with the DB
				connect = DriverManager
						.getConnection("jdbc:mysql://localhost/amazon?"
								+ "user=root&password=");
				// Statements allow to issue SQL queries to the database
				statement = connect.createStatement();

				//check new entry
				String sql = ("SELECT COUNT(link) FROM webLinks");
				ResultSet rs = statement.executeQuery(sql);
				while (rs.next()) {
					int linkCount = rs.getInt(1);
					System.out.println(linkCount);
					if (linkCount > webLinkCounter) {
						sql = ("SELECT link FROM webLinks ORDER BY timestamp DESC LIMIT "
								+ (linkCount - webLinkCounter) + ";");
						Statement statement2 = connect.createStatement();
						ResultSet rs_link = statement2.executeQuery(sql);
						while (rs_link.next()) {
							String link = rs_link.getString("link");
							System.out.println(link);
							
							//check if table price has this record already
							sql=("SELECT link From price WHERE price.link='"+link+"'");
							Statement statement3 = connect.createStatement();
							ResultSet rs3 = statement3.executeQuery(sql);
							if(rs3.next()){
								break;
							}
							
							Document doc = Jsoup.connect(link).get();

							// get price
							Element xp=null;
							if(doc.select("#priceblock_dealprice").first()!=null){
								xp = doc.select("#priceblock_dealprice")
										.first();
							}
							else if(doc.select("#priceblock_ourprice").first()!=null){
								xp = doc.select("#priceblock_ourprice")
										.first();
							}
							
							String xpString = xp.text();
							System.out.println(xpString);
							xpString = xpString.replace("$", "");
							xpString = xpString.replace(",", "");
							float price = Float.parseFloat(xpString);
							System.out.println(price);

							// get title
							xp = doc.select("#productTitle").first();
							String title = xp.text();
							System.out.println(title);

							sql = ("INSERT INTO price(link, title, curPrice, timestamp) VALUES (\""
									+ link
									+ "\", "
									+ "\""
									+ title
									+ "\", "
									+ "\"" + price + "\", " + "NOW()" + ")");
							System.out.println(sql);
							preparedStatement = connect.prepareStatement(sql);
							preparedStatement.executeUpdate();
						}

						webLinkCounter = linkCount;
					}
				}
				
				//check new price
				sql = ("SELECT * FROM price");
				rs = statement.executeQuery(sql);
				while (rs.next()) {
					String link = rs.getString("link");
					System.out.println(link);
					Document doc = Jsoup
							.connect(link)
							.userAgent(
									"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0")
							.referrer("http://www.google.com").get();
					
					//get price from link
					Element xp=null;
					if(doc.select("#priceblock_dealprice").first()!=null){
						xp = doc.select("#priceblock_dealprice")
								.first();
					}
					else if(doc.select("#priceblock_ourprice").first()!=null){
						xp = doc.select("#priceblock_ourprice")
								.first();
					}
					
					String xpString = xp.text();
					System.out.println(xpString);
					xpString = xpString.replace("$", "");
					xpString = xpString.replace(",", "");
					float webPrice = Float.parseFloat(xpString);
					
					float curPrice=rs.getFloat("curPrice");
					if(webPrice<curPrice){
						sql=("SELECT email FROM webLinks WHERE webLinks.link='"+link+"'");
						Statement statement2 = connect.createStatement();
						ResultSet rs2 = statement2.executeQuery(sql);
						while(rs2.next()){
							String to = rs2.getString("email");
							String product_title=rs.getString("title");
							sendEmail(to,webPrice,product_title);
						}
						
					}
					curPrice=webPrice;
					sql = ("UPDATE price SET curPrice="+curPrice+", timestamp=NOW() WHERE link='"+link+"'");
					System.out.println(sql);
					preparedStatement = connect.prepareStatement(sql);
					preparedStatement.executeUpdate();
				}
				
				Thread.sleep(1000 * 60);
				

			} catch (Exception e) {
				throw e;
			} finally {
				try {

					if (statement != null) {
						statement.close();
					}

					if (connect != null) {
						connect.close();
					}
				} catch (Exception e) {

				}
			}

			
		}

	}
	
//	public static void sendEmail(String to,float webPrice,String product_title) throws Exception{
//        Properties props = new Properties();
//        Session session = Session.getDefaultInstance(props, null);
//
//        String msgBody = "...";
//
//        try {
//            Message msg = new MimeMessage(session);
//            msg.setFrom(new InternetAddress("admin@example.com", "Example.com Admin"));
//            msg.addRecipient(Message.RecipientType.TO,
//                             new InternetAddress("user@example.com", "Mr. User"));
//            msg.setSubject("Your Example.com account has been activated");
//            msg.setText(msgBody);
//            Transport.send(msg);
//
//	      }catch (MessagingException mex) {
//	         mex.printStackTrace();
//	      }
//	}
	

}
